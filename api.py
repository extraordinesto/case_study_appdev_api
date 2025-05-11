from flask import Flask, request, jsonify, render_template
import mysql.connector
from flask_cors import CORS
from datetime import datetime
import hashlib
import torch
from PIL import Image
import io

# Initialize Flask app and enable CORS
app = Flask(__name__)
CORS(app)

# Load YOLOv5 model
model = torch.hub.load('ultralytics/yolov5', 'yolov5s', trust_repo=True)

# MySQL connection setup
db = mysql.connector.connect(
    host="localhost",
    user="root",
    password="",
    database="shop_inventory"
)

# 1. User login endpoint
@app.route('/login', methods=['POST'])
def login():
    data = request.get_json()
    username = data.get('username')
    password = hashlib.md5(data.get('password').encode()).hexdigest()

    cursor = db.cursor(dictionary=True)
    query = "SELECT * FROM user WHERE username = %s AND password = %s"
    cursor.execute(query, (username, password))
    result = cursor.fetchone()

    if result:
        return jsonify({"success": True, "userData": result})
    else:
        return jsonify({"success": False})

# 2. User registration endpoint
@app.route('/signup', methods=['POST'])
def register():
    data = request.get_json()
    name = data.get('fullName')
    username = data.get('username')
    password = hashlib.md5(data.get('password').encode()).hexdigest()

    cursor = db.cursor()
    query = "INSERT INTO user (fullName, username, password) VALUES (%s, %s, %s)"
    try:
        cursor.execute(query, (name, username, password))
        db.commit()
        return jsonify({"success": True})
    except:
        db.rollback()
        return jsonify({"success": False})

# 3. Check if username exists endpoint
@app.route('/validate-username', methods=['POST'])
def check_username():
    data = request.get_json()
    username = data.get('username')

    cursor = db.cursor()
    query = "SELECT * FROM user WHERE username = %s"
    cursor.execute(query, (username,))
    if cursor.fetchone():
        return jsonify({"usernameFound": True})
    else:
        return jsonify({"usernameFound": False})
    
@app.route('/item', methods=['GET'])
def item():
    cursor = db.cursor(dictionary=True)
    cursor.execute("SELECT * FROM item")
    item = cursor.fetchall()
    
    return jsonify(item)

@app.route('/customer', methods=['GET'])
def customer():
    cursor = db.cursor(dictionary=True)
    cursor.execute("SELECT * FROM customer")
    customer = cursor.fetchall()
    
    return jsonify(customer)

@app.route("/add_customer", methods=["POST"])
def add_customer():
    try:
        data = request.json
        print("Received Data:", data)  # Debugging

        fullName = data["fullName"]
        email = data["email"]
        mobile = data["mobile"]
        phone2 = data["phone2"]
        address = data["address"]
        address2 = data["address2"]
        city = data["city"]
        district = data["district"]
        status = data["status"]

        cursor = db.cursor()

        insert_query = """INSERT INTO customer 
                          (fullName, email, mobile, phone2, address, address2, city, district, status) 
                          VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s)"""
        
        values = (fullName, email, mobile, phone2, address, address2, city, district, status)

        cursor.execute(insert_query, values)
        db.commit()

        return jsonify({"message": "Customer added successfully!"}), 201

    except Exception as e:
        print("Error:", str(e))  # Debugging
        return jsonify({"error": "Failed to add product", "details": str(e)}), 500
    
    finally:
        cursor.close()

@app.route('/product/transaction', methods=['POST'])
def product_transaction():
    data = request.json
    transaction_type = data.get('type')
    cursor = None

    try:
        cursor = db.cursor()

        if transaction_type == 'IN':
            # Check if itemNumber or itemName already exists
            cursor.execute(
                "SELECT itemNumber, itemName FROM item WHERE itemNumber = %s OR itemName = %s",
                (data['itemNumber'], data['itemName'])
            )
            existing = cursor.fetchall()

            item_number_exists = any(row[0] == data['itemNumber'] for row in existing)
            item_name_exists = any(row[1] == data['itemName'] for row in existing)

            if item_number_exists and item_name_exists:
                return jsonify({'status': 'error', 'message': 'ItemNumber and ItemName already exist'}), 400
            elif item_number_exists:
                return jsonify({'status': 'error', 'message': 'ItemNumber already exists'}), 400
            elif item_name_exists:
                return jsonify({'status': 'error', 'message': 'ItemName already exists'}), 400

            # Insert new item
            insert_query = """
                INSERT INTO item 
                (itemNumber, itemName, discount, stock, unitPrice, imageURL, status, description)
                VALUES (%s, %s, %s, %s, %s, %s, %s, %s)
            """
            values = (
                data['itemNumber'],
                data['itemName'],
                float(data['discount']),
                int(data['stock']),
                float(data['unitPrice']),
                data.get('imageURL', 'imageNotAvailable.jpg'),
                data['status'],
                data['description']
            )
            cursor.execute(insert_query, values)
            db.commit()
            return jsonify({'status': 'success', 'message': 'Product added successfully'})

        elif transaction_type == 'OUT':
            item_number = data['itemNumber']
            quantity_requested = int(data['quantity'])

            # Check stock availability
            cursor.execute("SELECT stock FROM item WHERE itemNumber = %s", (item_number,))
            result = cursor.fetchone()
            cursor.fetchall()  # Consume any unread result if needed

            if not result:
                return jsonify({'status': 'error', 'message': 'Item not found'}), 400

            current_stock = result[0]

            if quantity_requested > current_stock:
                return jsonify({'status': 'error', 'message': 'Insufficient stock'}), 400

            # Deduct stock
            update_stock_query = "UPDATE item SET stock = stock - %s WHERE itemNumber = %s"
            cursor.execute(update_stock_query, (quantity_requested, item_number))

            # Record sale
            insert_sale_query = """
                INSERT INTO sale 
                (itemNumber, customerID, customerName, itemName, saleDate, discount, quantity, unitPrice)
                VALUES (%s, %s, %s, %s, %s, %s, %s, %s)
            """
            sale_values = (
                item_number,
                data['customerID'],
                data['customerName'],
                data['itemName'],
                datetime.now().strftime('%Y-%m-%d %H:%M:%S'),
                float(data['discount']),
                quantity_requested,
                float(data['unitPrice'])
            )
            cursor.execute(insert_sale_query, sale_values)
            db.commit()
            return jsonify({'status': 'success', 'message': 'Sale recorded and stock updated'})

        else:
            return jsonify({'status': 'error', 'message': 'Invalid transaction type'}), 400

    except Exception as e:
        print("Transaction error:", str(e))
        return jsonify({'status': 'error', 'message': str(e)}), 500

    finally:
        if cursor:
            try:
                cursor.close()
            except Exception as close_error:
                print("Cursor close error:", close_error)

@app.route("/add_item", methods=["POST"])
def add_item():
    try:
        data = request.json
        print("Received Data:", data)

        itemNumber = data["itemNumber"]
        itemName = data["itemName"]
        discount = data["discount"]
        stock = data["stock"]
        unitPrice = data["unitPrice"]
        imageURL = data["imageURL"]
        status = data["status"]
        description = data["description"]

        cursor = db.cursor(buffered=True)  # FIX: buffered cursor

        # Check for existing itemNumber or itemName
        check_query = "SELECT * FROM item WHERE itemNumber = %s OR itemName = %s"
        cursor.execute(check_query, (itemNumber, itemName))
        existing_item = cursor.fetchone()

        if existing_item:
            cursor.close()
            return jsonify({
                "error": "Item already exists",
                "details": "An item with the same number or name already exists."
            }), 409

        # Insert new item
        insert_query = """
            INSERT INTO item 
            (itemNumber, itemName, discount, stock, unitPrice, imageURL, status, description) 
            VALUES (%s, %s, %s, %s, %s, %s, %s, %s)
        """
        values = (itemNumber, itemName, discount, stock, unitPrice, imageURL, status, description)

        cursor.execute(insert_query, values)
        db.commit()
        cursor.close()

        return jsonify({"message": "Product added successfully!"}), 201

    except Exception as e:
        print("Error:", str(e))
        return jsonify({"error": "Failed to add product", "details": str(e)}), 500

# AI-Image Recognition
@app.route('/')
def home():
    return render_template('ai_image_recognition.php')

@app.route('/detect-image', methods=['POST'])
def detect_image():
    if 'image' not in request.files:
        return jsonify({'error': 'No image uploaded'}), 400

    file = request.files['image']
    img_bytes = file.read()
    img = Image.open(io.BytesIO(img_bytes))
    results = model(img)
    labels = results.pandas().xyxy[0]

    if labels.empty:
        return jsonify({'product_name': 'Unknown'}), 200

    top_label = labels.iloc[0]
    name = top_label['name']
    confidence = float(top_label['confidence'])

    return jsonify({'product_name': name, 'confidence': confidence})

# Run the app
if __name__ == '__main__':
    app.run(debug=True, host='0.0.0.0', port=5000)

from flask import Flask, request, jsonify
import mysql.connector
import hashlib
from flask_cors import CORS
from datetime import datetime

# Initialize Flask app and enable CORS
app = Flask(__name__)
CORS(app)

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

@app.route('/product/transaction', methods=['POST'])
def product_transaction():
    data = request.json
    transaction_type = data['type']  # 'IN' or 'OUT'

    try:
        cursor = db.cursor()

        if transaction_type == 'IN':
            query = """
                INSERT INTO item 
                (itemNumber, itemName, discount, stock, unitPrice, imageURL, status, description)
                VALUES (%s, %s, %s, %s, %s, %s, %s, %s)
            """
            values = (
                data['itemNumber'],
                data['itemName'],
                data['discount'],
                data['stock'],
                data['unitPrice'],
                data.get('imageURL', 'imageNotAvailable.jpg'),
                data['status'],
                data['description']
            )
            cursor.execute(query, values)
            db.commit()
            return jsonify({'status': 'success', 'message': 'Product added successfully'})

        elif transaction_type == 'OUT':
            update_stock_query = "UPDATE item SET stockQuantity = stockQuantity - %s WHERE itemNumber = %s"
            cursor.execute(update_stock_query, (data['quantity'], data['itemNumber']))

            insert_sale_query = """
                INSERT INTO sale 
                (itemNumber, customerID, customerName, itemName, saleDate, discount, quantity, unitPrice)
                VALUES (%s, %s, %s, %s, %s, %s, %s, %s)
            """
            sale_values = (
                data['itemNumber'],
                data['customerID'],
                data['customerName'],
                data['itemName'],
                datetime.now().strftime('%Y-%m-%d %H:%M:%S'),
                data['discount'],
                data['quantity'],
                data['unitPrice']
            )
            cursor.execute(insert_sale_query, sale_values)
            db.commit()
            return jsonify({'status': 'success', 'message': 'Sale recorded and stock updated'})

        else:
            return jsonify({'status': 'error', 'message': 'Invalid transaction type'}), 400

    except Exception as e:
        print("Transaction error:", str(e))
        return jsonify({'status': 'error', 'message': str(e)})

    finally:
        cursor.close()
        
# Run the app
if __name__ == '__main__':
    app.run(debug=True, host='0.0.0.0', port=5000)

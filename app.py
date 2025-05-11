from flask import Flask, request, jsonify, render_template
from flask_cors import CORS
import torch
from PIL import Image
import io

app = Flask(__name__)
CORS(app)

# Load YOLOv5 model
model = torch.hub.load('ultralytics/yolov5', 'yolov5s', trust_repo=True)

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

if __name__ == '__main__':
    app.run(debug=True)

import cv2
import numpy as np
import mediapipe as mp
from flask import Flask, request, jsonify

app = Flask(__name__)
mp_pose = mp.solutions.pose

def analyze_body(image_path):
    image = cv2.imread(image_path)
    image = cv2.cvtColor(image, cv2.COLOR_BGR2RGB)
    
    with mp_pose.Pose(static_image_mode=True) as pose:
        results = pose.process(image)
        
        if results.pose_landmarks:
            landmarks = results.pose_landmarks.landmark
            shoulder_width = abs(landmarks[mp_pose.PoseLandmark.LEFT_SHOULDER].x - 
                                 landmarks[mp_pose.PoseLandmark.RIGHT_SHOULDER].x)
            
            height = abs(landmarks[mp_pose.PoseLandmark.NOSE].y - 
                         landmarks[mp_pose.PoseLandmark.LEFT_ANKLE].y)
            
            body_ratio = shoulder_width / height
            
            if body_ratio < 0.3:
                size = "S"
            elif 0.3 <= body_ratio < 0.4:
                size = "M"
            else:
                size = "L"
                
            return {"shoulder_width": shoulder_width, "height": height, "size": size}
    return {"error": "No body detected"}

@app.route('/analyze', methods=['POST'])
def analyze():
    file = request.files['image']
    file_path = "uploaded_image.jpg"
    file.save(file_path)
    result = analyze_body(file_path)
    return jsonify(result)

if __name__ == '__main__':
    app.run(debug=True)

import sys
import joblib
import json

# Load model
model = joblib.load('model.pkl')

# Inputs from PHP (sys.argv or stdin)
sugar = float(sys.argv[1])
sleep = float(sys.argv[2])
bp = float(sys.argv[3])
oxygen = float(sys.argv[4])

# Predict
input_data = [[sugar, sleep, bp, oxygen]]
prediction = model.predict(input_data)[0]

# Generate insights
tips = []
if sugar > 140:
    tips.append("High sugar level. Reduce sugar intake and monitor regularly.")
if sleep < 5:
    tips.append("Irregular sleep. Aim for 6–8 hours of consistent sleep.")
if bp > 140:
    tips.append("High BP detected. Consider reducing salt intake.")
if oxygen < 92:
    tips.append("Low oxygen level. Seek medical attention if persistent.")

result = {
    "risk": "Yes" if prediction == 1 else "No",
    "tips": tips
}

print(json.dumps(result))

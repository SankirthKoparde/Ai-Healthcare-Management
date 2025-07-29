import pandas as pd
from sklearn.model_selection import train_test_split
from sklearn.tree import DecisionTreeClassifier
import joblib

# Load dataset
df = pd.read_csv('health_dataset.csv')  # Manually labeled or exported from system

# Features & Target
X = df[['sugar_level', 'sleep_hours', 'bp_systolic', 'oxygen_level']]
y = df['risk_level']  # 0 or 1

# Train model
X_train, X_test, y_train, y_test = train_test_split(X, y, test_size=0.2)
model = DecisionTreeClassifier()
model.fit(X_train, y_train)

# Save model
joblib.dump(model, 'model.pkl')
print("Model trained and saved.")

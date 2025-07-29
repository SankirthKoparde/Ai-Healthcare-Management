# eDoc Healthcare System

A web-based healthcare channeling and AI insights platform.

## Features

- Patient and doctor management
- Appointment scheduling
- Health data entry and visualization
- AI-powered health risk insights
- Healthcare chatbot for symptom checking

## Setup

1. **Database**: Import `SQL_Database_edoc.sql` into your MySQL server.
2. **Backend**: PHP 7+, MySQL, Python 3.x (for AI scripts).
3. **Python AI**: Install dependencies:
   ```
   pip install -r hc_bot/requirements.txt
   ```
4. **Run Chatbot**: Start Flask chatbot:
   ```
   cd hc_bot
   python chat_bot.py
   ```
5. **Web App**: Access via your local web server (e.g., XAMPP, WAMP).

## Folders

- `/patient` - Patient dashboard and features
- `/doctor` - Doctor dashboard and features
- `/health` - Health data forms and graphs
- `/python` - AI model scripts and insights
- `/hc_bot` - Chatbot source code

## Authors

- Hashen Udara (and contributors)

---

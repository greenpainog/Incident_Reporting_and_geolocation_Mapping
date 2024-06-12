from flask import Flask, render_template
import mysql.connector
import pandas as pd

app = Flask(__name__)

@app.route('/')
def show_recent_updates():
    # Connect to the MySQL database
    conn = mysql.connector.connect(
        host="localhost",
        user="root",
        password="",
        database="tweetmapping"
    )
    cursor = conn.cursor()

    # Query to select recently updated entries
    query = """
    SELECT id, text, spacy_woi, last_updated
    FROM policetweets
    WHERE last_updated > NOW() - INTERVAL 1 DAY;
    """
    cursor.execute(query)
    rows = cursor.fetchall()
    df = pd.DataFrame(rows, columns=['id', 'text', 'spacy_woi', 'last_updated'])

    cursor.close()
    conn.close()

    # Render the HTML template with the DataFrame
    return render_template('latestupdatespage.html', tables=[df.to_html(classes='data', header="true")])

if __name__ == '__main__':
    app.run(debug=True)

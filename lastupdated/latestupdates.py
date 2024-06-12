import pandas as pd
import spacy
import mysql.connector

# Load Greek NLP model
nlp = spacy.load("el_core_news_sm")

# Connect to the MySQL database
conn = mysql.connector.connect(
    host="localhost",
    user="root",
    password="",
    database="tweetmapping"
)
cursor = conn.cursor()

# Verify the column names
cursor.execute("DESCRIBE policetweets;")
columns = cursor.fetchall()
for column in columns:
    print(column)

# Adjust the column name in the SQL query based on the actual column name
query = "SELECT id, text FROM policetweets WHERE latitude IS NULL AND longitude IS NULL AND category > 0;"
cursor.execute(query)
rows = cursor.fetchall()

# Convert rows to DataFrame
df = pd.DataFrame(rows, columns=['id', 'text'])

# Print a few rows to verify the data
print("Sample tweets from database:")
print(df.head())

# Function to extract location
def extract_location(text):
    doc = nlp(text)
    locations = [ent.text for ent in doc.ents if ent.label_ == 'GPE' or ent.label_ == 'LOC']
    return locations

# Process each tweet to extract location
locations = []
success_count = 0
for index, row in df.iterrows():
    print(f"Processing tweet: {row['text']}")  # Debugging: Print each tweet text
    extracted_locations = extract_location(row['text'])
    print(f"Extracted locations: {extracted_locations}")  # Debugging: Print extracted locations
    if extracted_locations:
        concatenated_locations = ', '.join(extracted_locations)  # Concatenate multiple locations
        locations.append((row['id'], concatenated_locations))
        success_count += 1
    else:
        locations.append((row['id'], None))

# Convert the results to a DataFrame
results_df = pd.DataFrame(locations, columns=['id', 'spacy_woi'])

# Replace NaN with None to handle null values correctly in the database
results_df = results_df.replace({pd.NA: None})

# Output the results or update the database as needed
print("Results DataFrame:")
print(results_df)

# Optionally, update the database with the new spacy_woi values
update_query = """
UPDATE policetweets
SET spacy_woi = %s, last_updated = CURRENT_TIMESTAMP
WHERE id = %s;
"""
for index, row in results_df.iterrows():
    cursor.execute(update_query, (row['spacy_woi'], row['id']))
    conn.commit()

# Print the number of successfully updated rows
print(f"Number of successfully extracted and updated locations: {success_count}")

# Close the database connection
cursor.close()
conn.close()

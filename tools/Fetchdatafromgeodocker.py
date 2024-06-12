import pandas as pd
import json

# Function to scrape data from a single page and return DataFrame
def scrape_page_to_dataframe(url):
    tables = pd.read_html(url)
    if tables:
        return tables[0]
    else:
        return None

# Base URLs of the webpages containing the tables
base_urls = ['https://geokeo.com/database/town/gr', 'https://geokeo.com/database/city/gr']

# Initialize an empty DataFrame
df = pd.DataFrame()

# Loop through each base URL
for base_url in base_urls:
    # Scrape data from page 1
    page_url = f'{base_url}/1/'
    page_df = scrape_page_to_dataframe(page_url)

    # If data is successfully extracted, continue scraping from subsequent pages
    if page_df is not None:
        print(f"Data extracted from {base_url}/1.")
        df = pd.concat([df, page_df], ignore_index=True)

        # Check if there are more pages
        for page_number in range(2, 4):  # Assuming there are 3 pages in total
            page_url = f'{base_url}/{page_number}/'
            page_df = scrape_page_to_dataframe(page_url)
            if page_df is not None:
                print(f"Data extracted from {base_url}/{page_number}.")
                df = pd.concat([df, page_df], ignore_index=True)
            else:
                print(f"No data found on {base_url}/{page_number}.")

# Save data to JSON file with proper encoding
with open('towns_in_greece_with_greek_names.json', 'a', encoding='utf-8') as f:
    json.dump(df.to_dict(orient='records'), f, indent=4, ensure_ascii=False)
print("Data appended to 'towns_in_greece_with_greek_names.json'")
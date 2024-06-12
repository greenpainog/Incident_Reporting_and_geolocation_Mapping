import sys
import spacy
import requests

def extract_locations(text):
    nlp = spacy.load("el_core_news_lg")
    doc = nlp(text)
    locations = [ent.text for ent in doc.ents if ent.label_ in ['GPE', 'LOC']]
    return locations

def get_detailed_location_info(location, api_key):
    location = requests.utils.quote(location)
    url = f"https://maps.googleapis.com/maps/api/geocode/json?address={location}&key={api_key}&language=el"
    response = requests.get(url)
    if response.status_code == 200:
        data = response.json()
        if data['status'] == 'OK':
            return data['results'][0]['formatted_address']
    return location

if __name__ == "__main__":
    sys.stdout.reconfigure(encoding='utf-8')
    sys.stderr.reconfigure(encoding='utf-8')

    if len(sys.argv) > 1:
        text = sys.argv[1]
        locations = extract_locations(text)
        if locations:
            api_key = 'AIzaSyCkXwhu864y_F4GSYBo0XyanJzHjI-S5iM'
            detailed_locations = [get_detailed_location_info(loc, api_key) for loc in locations]
            concatenated_locations = ', '.join(detailed_locations)
            print(concatenated_locations)
        else:
            print("")
    else:
        print("")

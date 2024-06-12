import re

# List of known locations (add more as needed)
known_locations = ["Πέλλας", "Αθήνα", "Ολυμπιακό Σκοπευτήριο Μαρκόπουλου"]

# Function to extract location using known locations
def extract_location_rule_based(text):
    locations = []
    for loc in known_locations:
        if re.search(r'\b' + re.escape(loc) + r'\b', text):
            locations.append(loc)
    return locations

# Test extraction
texts = [
    "❗❗ μαχαιριες στην οδο εγνατιας 15 ,θεσσαλονικη	",
    "Η Άσκηση PRINCE CBRNE θα πραγματοποιηθεί στην Αθήνα στο Ολυμπιακό Σκοπευτήριο Μαρκόπουλου."
]

for text in texts:
    locations = extract_location_rule_based(text)
    print(f"Text: {text}\nExtracted Locations: {locations}\n")

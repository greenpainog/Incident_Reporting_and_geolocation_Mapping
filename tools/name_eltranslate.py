import json

def add_name_el(json_file):
    # Load JSON data from file
    with open(json_file, 'r', encoding='utf-8') as f:
        data = json.load(f)

    # Add "name_el" field to each entry
    for entry in data:
        other_language_names = entry.get('Other Language Names', '')
        name_el = None

        # Check if the entry has Greek name in "Other Language Names"
        if other_language_names:
            # Split the field by commas
            parts = other_language_names.split(',')
            # Iterate over parts to find the Greek name
            for part in parts:
                # Check if the part contains "name:el"
                if 'name:el' in part:
                    # Extract the Greek name from the part
                    name_el = part.split('=>')[1].strip('" ')
                    break  # Exit the loop if Greek name is found

        # If Greek name is found, use it for "name_el"; otherwise, use English name
        if name_el:
            entry['name_el'] = name_el
        else:
            entry['name_el'] = entry['Name']

    # Write the modified data back to the JSON file
    with open(json_file, 'w', encoding='utf-8') as f:
        json.dump(data, f, indent=4, ensure_ascii=False)

# Example usage
add_name_el('towns_in_greece_with_greek_names.json')

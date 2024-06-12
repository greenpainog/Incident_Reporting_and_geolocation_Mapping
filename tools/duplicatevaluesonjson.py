import json

def remove_duplicates(json_file):
    # Load JSON data from file
    with open(json_file, 'r', encoding='utf-8') as f:
        data = json.load(f)

    # Create a set to store unique "name" values
    unique_names = set()
    unique_data = []
    duplicate_count = 0  # Counter for duplicate entries

    # Iterate over each entry in the data
    for entry in data:
        name = entry['Name']
        # Check if the "name" value is unique
        if name not in unique_names:
            # Add the entry to the unique data list
            unique_data.append(entry)
            # Add the "name" value to the set
            unique_names.add(name)
        else:
            print(f"Duplicate entry found for '{name}' and removed.")
            duplicate_count += 1  # Increment counter for duplicate entries

    # Write the unique data back to the JSON file
    with open(json_file, 'w', encoding='utf-8') as f:
        json.dump(unique_data, f, indent=4, ensure_ascii=False)

    # Print the number of duplicate entries removed
    print(f"Total duplicate entries removed: {duplicate_count}")

# Example usage
remove_duplicates('towns_in_greece_with_greek_names.json')

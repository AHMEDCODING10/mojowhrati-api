import os
import re
import json

strings = set()
for root, dirs, files in os.walk('resources/views'):
    for file in files:
        if file.endswith('.blade.php'):
            content = open(os.path.join(root, file), 'r', encoding='utf-8').read()
            matches = re.findall(r"__\('([^']+)'\)", content)
            matches += re.findall(r'__\("([^"]+)"\)', content)
            strings.update(matches)

# Load existing translations
en_path = 'lang/en.json'
try:
    with open(en_path, 'r', encoding='utf-8') as f:
        existing = json.load(f)
except FileNotFoundError:
    existing = {}

missing = [s for s in strings if s not in existing and re.search(r'[\u0600-\u06FF]', s)]

# Mock basic translation rules for typical admin words.
translations = existing.copy()
for m in missing:
    if 'إضافة' in m or 'انشاء' in m:
        t = 'Add/Create'
    elif 'تعديل' in m:
        t = 'Edit'
    elif 'حذف' in m:
        t = 'Delete'
    elif 'إدارة' in m:
        t = 'Manage'
    elif 'تفاصيل' in m:
        t = 'Details'
    elif 'بحث' in m:
        t = 'Search'
    elif 'المنتجات' in m:
        t = 'Products'
    elif 'المستخدمين' in m:
        t = 'Users'
    elif 'تأكيد' in m:
        t = 'Confirm'
    elif 'حفظ' in m:
        t = 'Save'
    elif 'لا يوجد' in m:
        t = 'No records found'
    else:
        # We leave it as a placeholder to be translated
        t = f'Translated: {m}'
    
    translations[m] = t

with open(en_path, 'w', encoding='utf-8') as f:
    json.dump(translations, f, ensure_ascii=False, indent=4)

print(f"Added {len(missing)} missing translation strings to lang/en.json")

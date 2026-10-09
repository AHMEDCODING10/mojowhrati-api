import os
import re

directories = [
    'resources/views/banners',
    'resources/views/bookings',
    'resources/views/categories',
    'resources/views/custom_designs',
    'resources/views/gold-prices',
    'resources/views/merchants',
    'resources/views/notifications',
    'resources/views/products',
    'resources/views/reports',
    'resources/views/settings',
    'resources/views/users'
]

def process_match(m):
    text = m.group(1).strip()
    # If it's already translated or just whitespace/newlines, skip
    if not text or '{' in text or '}' in text or '@' in text or '__(' in text:
        return m.group(0)
    
    # Check if contains Arabic characters
    if not re.search(r'[\u0600-\u06FF]', text):
        return m.group(0)
        
    return m.group(0).replace(text, f"{{{{ __('{text}') }}}}")

def process_file(file_path):
    with open(file_path, 'r', encoding='utf-8') as f:
        content = f.read()

    # Pattern 1: Text between HTML tags > Arabic Text <
    # We must exclude lines that already have Blade tags or HTML tags inside the text block
    content = re.sub(r'>([^<>{]*?[\u0600-\u06FF][^<>{]*?)<', process_match, content)

    # Pattern 2: placeholder="Arabic Text"
    def ph_match(m):
        attr = m.group(1) # placeholder or title
        text = m.group(2)
        if '__(' in text or '{' in text: return m.group(0)
        if not re.search(r'[\u0600-\u06FF]', text): return m.group(0)
        return f'{attr}="{{{{ __(\'{text}\') }}}}"'
        
    content = re.sub(r'(placeholder|title)="([^"]*?[\u0600-\u06FF][^"]*?)"', ph_match, content)
    content = re.sub(r"(placeholder|title)='([^']*?[\u0600-\u06FF][^']*?)'", ph_match, content)

    # Pattern 3: @section('title', 'Arabic Text')
    def sec_match(m):
        sec = m.group(1)
        text = m.group(2)
        if '__(' in text or '{' in text: return m.group(0)
        if not re.search(r'[\u0600-\u06FF]', text): return m.group(0)
        return f"@section('{sec}', __('{text}'))"
        
    content = re.sub(r"@section\('([^']+)',\s*'([^']*?[\u0600-\u06FF][^']*?)'\)", sec_match, content)

    # Pattern 4: confirm('Arabic') inside JS/HTML
    def conf_match(m):
        text = m.group(1)
        if '__(' in text or '{' in text: return m.group(0)
        if not re.search(r'[\u0600-\u06FF]', text): return m.group(0)
        return f"confirm('{{{{ __(\'{text}\') }}}}')"
        
    content = re.sub(r"confirm\('([^']*?[\u0600-\u06FF][^']*?)'\)", conf_match, content)

    with open(file_path, 'w', encoding='utf-8') as f:
        f.write(content)

for d in directories:
    if os.path.exists(d):
        for root, dirs, files in os.walk(d):
            for file in files:
                if file.endswith('.blade.php'):
                    process_file(os.path.join(root, file))

print("Translation wrapping complete!")

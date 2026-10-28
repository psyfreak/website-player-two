#!/usr/bin/env python3
"""Download original logo assets and update references.ts to local paths. Run where entre.prenerds.com is reachable."""
from pathlib import Path
from urllib.request import urlopen, Request
import re
src=Path(__file__).resolve().parents[1]/'src/data/references.ts'
text=src.read_text()
files=re.findall(r"\['[^']+','([^']+)'\]",text)
base='https://entre.prenerds.com/images/client/'
out=Path(__file__).resolve().parents[1]/'public/images/references'
out.mkdir(parents=True,exist_ok=True)
failed=[]
for file in files:
 try:
  with urlopen(Request(base+file,headers={'User-Agent':'Mozilla/5.0'}),timeout=20) as response:
   content=response.read()
   if not content:raise ValueError('Empty image')
   (out/file).write_bytes(content)
  print('OK',file)
 except Exception as exc:
  print('FAILED',file,exc);failed.append(file)
if failed:
 print(f'{len(failed)} files failed. URLs remain unchanged; try again later.')
else:
 text=text.replace("const base = 'https://entre.prenerds.com/images/client/';", "const base = '/images/references/';")
 src.write_text(text)
 print('All logos downloaded; switched Astro to local images.')

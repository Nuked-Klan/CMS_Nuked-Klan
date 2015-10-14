#!/usr/bin/python
import os
import shutil
import zipfile
import zlib
import os.path
# Note : Need python3 in order to run !

file = 'Nuked-Klan_v1.7.13.zip'

def _ignore(src, name):
    if ((src == './UPLOAD/') or (src == './/UPLOAD/')):
        print (name)
        return ['RESTDIR', 'conf.inc.php', file, 'Thumbs.db', '.DS_Store', '.gitignore', '.git', '.hg', '.svn', 'make.py']
    else:
        return []

def _RecImport(src, dst, zip):
    elements = os.listdir(src)
    ignored = _ignore('.' + dst, elements)
    for delete in ignored:
        try:
            elements.remove(delete)
        except ValueError:
            pass

    for ele in elements:
        if os.path.isfile(src + ele):
            zip.write(src + ele, dst + ele)
            print (dst + ele)
        elif os.path.isdir(src + ele):
            _RecImport(src + ele + '/', dst + ele + '/', zip)

try:
    shutil.rmtree('tmp')
except OSError:
    pass

try:
    os.remove(file)
except OSError:
    pass

zip = zipfile.ZipFile(file, 'w', zipfile.ZIP_DEFLATED, False)
_RecImport('RESTDIR/', '/', zip)
_RecImport('./', '/UPLOAD/', zip)

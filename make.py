#!/usr/bin/python
import os
import shutil
import zipfile
import zlib
import os.path

def _ignore(src, name ):
    if src == './UPLOAD/':
        print name
        return ['RESTDIR', 'conf.inc.php', 'Nuked-Klan.zip', '.hg', 'make.py']
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
            print dst + ele
        elif os.path.isdir(src + ele):
            _RecImport(src + ele + '/', dst + ele + '/', zip)

try:
    shutil.rmtree('tmp')
except OSError:
    pass

file = 'Nuked-Klan.zip'

try:
    os.remove(file)
except OSError:
    pass

zip = zipfile.ZipFile(file, 'w', zipfile.ZIP_DEFLATED, False)
_RecImport('RESTDIR/', '/', zip)
_RecImport('./', '/UPLOAD/', zip)

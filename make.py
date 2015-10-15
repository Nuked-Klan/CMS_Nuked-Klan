#!/usr/bin/python
import os
import shutil
import zipfile
import zlib
import os.path
import sys
import re
# Note : Need python3 in order to run !

def _getVersion():
    with open('INSTALL/config.php') as f:
        for line in f:
            version_search = re.search('\'nkVersion\'[\s]+=>[\s]+\'(.*)\'', line)

            if version_search:
                return version_search.group(1)

    sys.exit('Version number no found !')

def _ignore(src, name):
    if ((src == './UPLOAD/') or (src == './/UPLOAD/')):
        return [['RESTDIR', 'conf.inc.php', file, 'Thumbs.db', 'ehthumbs.db', '.DS_Store', '.Spotlight-V100', '.Trashes', '.gitignore', '.git', 'make.py'], ['config_save_.*\.php', '*\.bak', 'thumbcache_.*\.db']]
    else:
        return [[], []]

def _RecImport(src, dst, zip):
    elements = os.listdir(src)
    ignored = _ignore('.' + dst, elements)

    for delete in ignored[0]:
        try:
            elements.remove(delete)
        except ValueError:
            pass

    for ele in elements:
        remove = False

        for delete in ignored[1]:
            test = re.search(delete, ele)

            if test :
                remove = True
                break

        if remove is not True:
            if os.path.isfile(src + ele):
                zip.write(src + ele, dst + ele)
                print (dst + ele)
            elif os.path.isdir(src + ele):
                _RecImport(src + ele + '/', dst + ele + '/', zip)


version = _getVersion()
file = 'Nuked-Klan_v' + version + '.zip'

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

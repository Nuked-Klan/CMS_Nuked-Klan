#!/usr/bin/python
import os
import os.path
import commands
# Note : Need python3 in order to run !

def _listPhpFileOfDirectory(src):
    elements = os.listdir(src)

    for ele in elements:
        if os.path.isfile(src + ele):
            filename, ext = os.path.splitext(src + ele)

            if (ext == '.php'):
                #print (src + ele)
                status, output = commands.getstatusoutput("php -l " + src + ele)

                if (status > 0):
                    print (output)

        elif os.path.isdir(src + ele):
            _listPhpFileOfDirectory(src + ele + '/')


_listPhpFileOfDirectory('./')
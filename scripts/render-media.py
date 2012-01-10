import os
import subprocess

srcdir = "/mnt/extra/Dropbox/Projekte/nowhere/nowhere_daten_einzeln"
mediadir = "/home/m/www/nowhere/htdocs/media/stones/"

for file in os.listdir(srcdir):
    if (os.path.isdir(srcdir + os.path.sep + file)):
        stonefile = srcdir + os.path.sep + file + os.path.sep + "stone.png"
        placefile = srcdir + os.path.sep + file + os.path.sep + "place.jpg"
        for size in ['150', '1024']:
            stonesizefile = mediadir + file + "-stone-" + size + ".png"
            if not os.path.isfile(stonesizefile):
                subprocess.call(["/usr/bin/convert", "-background", "transparent", "-scale", "%sx%s" % (size, size), '-gravity', 'center', '-extent', "%sx%s" % (size, size), stonefile, stonesizefile])
                print(stonesizefile)
            placesizefile = mediadir + file + "-place-" + size + ".jpg"
            if not os.path.isfile(placesizefile):
                subprocess.call(["/usr/bin/convert", "-thumbnail", "%sx%s^" % (size, size), "-gravity", "center", "-extent", "%sx%s" % (size, size), placefile, placesizefile])
                print(placesizefile)
        # Fullscreen
        placefullsizefile = mediadir + file + "-place-2048.jpg"
        if not os.path.isfile(placefullsizefile):
            subprocess.call(["/usr/bin/convert", "-thumbnail", "2048x2048^", placefile, placefullsizefile])
            print(placefullsizefile)
        stonefullsizefile = mediadir + file + "-stone-2048.png"
        if not os.path.isfile(stonefullsizefile):
            subprocess.call(["/usr/bin/convert", "-background", "transparent", "-thumbnail", "2048x2048^", stonefile, stonefullsizefile])
            print(stonefullsizefile)

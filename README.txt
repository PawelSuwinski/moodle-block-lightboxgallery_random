Block  Lightbox Gallery Randomizer
by Paweł Suwiński <dracono@wp.pl> (or <psuw@wp.pl>)

Block displays a thumbnail of random image from a course's lightboxgallery.


* Dependencies 

Block depends on Lightboxgallery module:
http://moodle.org/mod/data/view.php?d=13&rid=1021&filter=1

Not tested with its ported to jQuery version but it should works as well 
(please let me know on success or failure):
http://moodle.org/plugins/view.php?plugin=mod_lightboxgallery

If  dependent module is unavailable (not installed or invisible)
lightboxgallery_random block is switched to invisible at site level and staying
unavailable too. Dependencies are checked on every first initialization of
block module.

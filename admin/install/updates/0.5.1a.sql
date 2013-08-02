# Version 0.5.1a;
# Migration(upgrade). Uses only if UPDATE proccess executes!;
# Prev version 0.5(initial);

SET foreign_key_checks = 0;

 RENAME TABLE #__newsletters TO #__newsletter_newsletters;

SET foreign_key_checks = 1;

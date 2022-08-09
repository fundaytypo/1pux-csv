# 1pux-csv
Convert 1Password .1pux to CSV, while keeping most data intact.
1Password's own CSV export is lossy, and the 1pux format it not supported everywhere.

I wrote this quick and dirty PHP script to get a CSV file with most of my info for import into KeePassXC.
The script splits output into several CSV files, one for each vault. Items that have been archived in 1Password are put in separate CSV files, to avoid mixing up active and archived data.

Additional info such as additional URLs, password history are appended to the notes section.
Licences, credit cards etc are appended to the notes section as a printed array dump. I didn't bother parsing this data due to it having many different fields depending on the item type, I simply just wanted to keep the array dump and manually edited the few relevant items of this type later.

Attachments are not kept, they are in a folder inside the 1pux file (which is really just a zip archive). You will need to manually add attachments to your password manager.

The export.data file is just a JSON text file, you should keep it and add it as an attachment in your new password manager, in case you need it for future reference. Keep the file safe, as it contains plain-text passwords! My script is not bug free, it's quick and dirty like I said. Use at your own risk :)

[1pux format info](https://support.1password.com/1pux-format/)

## Usage
You will need to have PHP installed, obviously.

Export to .1pux from 1Password, extract the contents of that file (you might need to rename the file to .zip depending on your system), and put the export.data file in the same directory as the script.

Output example:

```
alex@sys:[~/pass]$ /usr/bin/php convert.php 
Accounts: 1

Account name: Alex Family
Name: Alex
E-mail: alex@example.com

Vaults: 3

Vault name: Private
Items: 324

Vault name: Shared
Items: 8

Vault name: Work
Items: 7
Writing to file: vault-Private-active.csv
Writing 184 items to file

Writing to file: vault-Private-archived.csv
Writing 140 items to file

Writing to file: vault-Shared-active.csv
Writing 8 items to file

Writing to file: vault-Work-active.csv
Writing 7 items to file
```

# 1pux-csv
1Password .1pux to CSV, while keeping most data

[1pux format info](https://support.1password.com/1pux-format/)

## Usage
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

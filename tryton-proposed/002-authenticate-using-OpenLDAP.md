## Configure SCC OpenLDAP directory

*Root password is in /usr/local/etc/openldap/slapd.conf (SSHA hash)*

* create SCC Domain and Manager entries.

```
$ ldapadd -x -D "cn=Manager,dc=scc,dc=org" -W -f /usr/local/maestro/ldif/domainmgr.ldif
```    

- create "People" organizational unit for user entries.

```
$ ldapadd -x -D "cn=Manager,dc=scc,dc=org" -W -f /usr/local/maestro/ldif/people.ldif
```    

## Update Master Person Spreadsheet and CSV

* Edit maestro/scc/ods/person.ods using OpenOffice
    * Add new persons on master sheet as needed (and/or modify)
    * Extend persons on ldap-People sheet as needed (and/or modify)
    * Use slappasswd to generate a password field

    
```
$ slappasswd -s appleton
{SSHA}Fa8SeAxWzHRAu+qQZJRAhH93JbZRICUg
```

*slappasswd generates a different string for the same password when executed multiple times because a different salt is used (the  default hash method SSHA is a salted SHA-1 hash, and the generated string includes both the salt and the hash).*
    
## Export LDAP data in CSV format from Master Person Spreadsheet

* Save/As ldap-People sheet to ods/person-ldap-People.csv
    * Export Text File options (typically as defaulted)
        * Character set: Western Europe (Windows-1252/WinLatin1)
        * Field delimeter: ","
        * Test delimeter: """
        * Save cell content as shown (preserves displayed number and text formatting)


## Convert LDAP data to LDIF format from CSV

* Convert person-ldap-People.csv to LDIF format using csv2ldif2.pl (from the SourceForge csv2ldif2 project, http://sourceforge.net/projects/csv2ldif2)

```
$ csv2ldif2.pl -b ou=People,dc=scc,dc=org < /usr/local/maestro/scc/ods/person-ldap-People.csv > /usr/local/maestro/scc/ldif/person-ldap-People.csv.ldif
```
    
*See Known Issues - Administrator User. person-ldap-People.csv.ldif must be edited before importing into OpenLDAP to add a second uid value to entry cn = "Administrator User".*

## Import Users into OpenLDAP

* Import SCC users into OpenLDAP.

```
$ ldapadd -x -D "cn=Manager,dc=scc,dc=org" -W -f /usr/local/maestro/ldif/person-ldap-People.csv.ldif
```
    

*Alternatively, copy /usr/local/maestro/scc/ldif/person-ldap-People.csv.ldif to a host system and import using phpLdapAdmin in web browser.*

## Configure Tryton to authenticate using OpenLDAP

If you have been following the workflows in order (i.e. you have completed 001-tryton-setup), you now need to configure Tryton to authenticate using OpenLDAP.

*Tryton will attempt to authenticate a user first with the OpenLDAP server, if authentication fails Tryton will then attempt to authenticate using the Tryton database.*

* Install Tryton LDAP modules onto server

```
# pip install trytond_ldap_connection
# pip install trytond_ldap_authentication
```

* Stop trytond server

```
# /usr/local/etc/rc.d/trytond stop
# /usr/local/etc/rc.d/trytond status
```

* Install Tryton LDAP modules into scc database

```
# /usr/local/bin/trytond -c /usr/local/etc/trytond.conf -i ldap_connection -d scc
# /usr/local/bin/trytond -c /usr/local/etc/trytond.conf -i ldap_authentication -d scc
```

* Update Tryton scc database to make LDAP menu available (workaround for https://bugs.tryton.org/issue2638)

```
# /usr/local/bin/trytond -c /usr/local/etc/trytond.conf -u all -d scc
```

* Start trytond server

```
# rm /var/run/trytond/trytond.pid
# /usr/local/etc/rc.d/trytond start
```

## Known Issues

### Administrator User

csv2ldif.pl does not correctly create the cn = "Administrator User" directory entry (or potentially any directeory entry that contains multiple attribute values). The resulting ldif file contains only one uid value, "administrator", when it should contain both "administrator" and "admin".
    
The Tryton ldap_authentication module authenticates by uid (login username). For example, it searches for uid = "admin" to authenticate the Tryton default administrator "admin" (if LDAP authentication fails, Tryton will then attempt to authenticate the user in the Tryton database). As a workaround for csv2ldif.pl behaviour, edit the output from csv2ldif2.pl and add the second uid ("uid: admin") to the cn = "Administrator User" entry. Alternatively, use a LDAP directory editor such as phpLDAPadmin to add the second uid value.
 
*Mantis authenticates its default administrator user "administrator" by searching for cn = "Administrator User" (not for the uid as Tryton does).*
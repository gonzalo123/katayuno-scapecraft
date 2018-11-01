# Katayuno-scapecraft

## Katas:
### kata1:
a partir del fichero: CPE1704TKS.txt

* Eliminar los primeros y últimos 100 bytes
* Eliminar 1 de cada 3 bytes (quedarse con el byte 1, 2, 4, 5, 7, 8, 10, …)
* Dar la vuelta a todo el fichero

### kata2:
a partir del fichero: CPE1704TKS-2.txt

* Eliminar los primeros y últimos 900 bytes
* Eliminar 1 de cada 3 bytes (quedarse con el byte 1, 2, 4, 5, 7, 8, 10, …)
* Dar la vuelta a todo el fichero

### kata3
A partir de los bytes eliminados de la kata1:

* Eliminar los primeros y últimos 900 bytes
* Eliminar 1 de cada 3 bytes (quedarse con el byte 1, 2, 4, 5, 7, 8, 10, …)
* Dar la vuelta a todo el fichero

## Notas:
Cuando decimos "Bytes eliminados" nos referimos a 
[primeros 100 bytes] + [bytes eliminados cuando eliminamos 1 de cada 3] + [últimos 100 bytes]


keypad1.png => 63500 bytes
con AddPosition(2) 95449 bytes => desecho 31949 bytes
con AddEvens() 127005 bytes  => desecho 63505 bytes

kataboom.png => 20100 bytes
genera un fichero de => 31949 (900 + 900)


16678
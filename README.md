# tp-preguntas

31/05
Reparada la vista Registro (cuando se agrego el mapa, el footer quedaba en medio y no se veian campos y botonoes, ademas de quedar etatica)
Eliminados archivos y funciones innecesarias.
Nueva base de datos, dividida:
    - Pimero se carga database.sql que crea la estructura y los datos fijos (esta armada para que a medida q agreguemos tablas nuevas ahí, no genere error ni duplique nada)
    - Despues se carga datos.sql, donde estan los datos de usuarios (editor, admin, master. clave 123), de las preguntas y de las respuestas.
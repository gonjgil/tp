# tp-preguntas

# revisar
cuando se pierde la partida solo se ve una pagina en blanco

# cambios
31/05
Reparada la vista Registro (cuando se agrego el mapa, el footer quedaba en medio y no se veian campos y botonoes, ademas de quedar etatica)
Eliminados archivos y funciones innecesarias.
Nueva base de datos, dividida:
    - Pimero se carga database.sql que crea la estructura y los datos fijos (esta armada para que a medida q agreguemos tablas nuevas ahí, no genere error ni duplique nada)
    - Despues se carga datos.sql, donde estan los datos de usuarios (editor, admin, master. clave 123), de las preguntas y de las respuestas.


31/05
cambios en el juego:

- si el jugador pone una respuesta incorrecta termina la partida
- se agrego a la tabla preguntas 3 columnas : time_answered ,times_ incorrec , dificult
- al aparecer una pregunta suma a la comuna correspondiente , si es incorrecta tambien y saca la % de dificultad
- hace lo mismo con la tabla usuario

cambios en el juego:
- buscar respuesta en base el jugador
- no se repite preguntas
- busca dificultad

1/06
Ahora hay 3 sql para cargar en orden: database -> usuarios -> preguntas

/***************************************/

9/06
Eliminado codigo innecesario en los controladores, para verificacion de roles (ya se manejaba desde el index)
Crear pregunta ahora muestra una lista con los nombres de las categorias, no el id

/***************************************/
10/06
Movido todo a raiz
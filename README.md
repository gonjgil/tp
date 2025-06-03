# tp-preguntas

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
OBSERVACIONES DE LA CLASE DEL 2 DE JUNIO
* Agregar y corregi exit() despues de cada header(...)
* Hay que armar el proyecto en una carpeta raiz / en lugar de /tp/
* Sobre repetición de preguntas:
    - Cuando un usuario vio todas las preguntas, se borra toda la lista de preguntas vistas asociada a ese usuario. Si ya pasaron todas las de su dificultad, queda a criterio de cada grupo si muestra de otra dificultad o se reinicia.
    - Charlar sobre logica de dificultad (guardamos respondidas e incorrectas, y propone calcularlo sobre correctas)
    - Ver que la dificultad se calcule recien despues de 10 preguntas (para dificultas de preguntas y de usuarios)
* Tiempo y logica Anti-Cheat: 
    - Hacer isset de la pregunta en la session. 
    - Guardar en session del back, q pregunta le di, en que tiempo de que partida; y cada intereccion del usuario va a generar cambios sobre eso. Guarda el tiempo en que se hizo la pregunta, y cuando contesta pide el now para comparar si pasaron menos de 10 segundos.
    - Que se muestre en el front
* No se devuelve la correcta en la misma pantalla de la pregunta
* Validación de email: ver screenshots. y pedirle a una IA que codee eso (dicho por el profe) y ver clase si es necesario
* Hashear el password
* Roles: validar sesion y baja y consultar (ver screenshots). Puede usarse un switch

# login-mdg2

Aplicacion web en `PHP + Apache + PostgreSQL` para el proyecto `Men's Wear`.

Incluye:

- login de usuarios
- dashboard principal
- registro de clientes
- gestion de pedidos
- conexion con `Supabase`
- despliegue con `Render`

## Requisitos

Para replicarlo en otra maquina se necesita:

- `Git`
- `Docker Desktop`
- una cuenta en `Supabase`
- opcional: una cuenta en `Render` para publicarlo

## 1. Clonar el repositorio

```bash
git clone https://github.com/JonaP07/login-mdg2.git
cd login-mdg2
```

## 2. Configurar la base de datos en Supabase

1. Crear un proyecto en `Supabase`.
2. Entrar a `SQL Editor`.
3. Ejecutar el script que esta en `database/database.sql`.
4. Verificar que se creen estas tablas:
   - `usuarios`
   - `clientes`
   - `productos`
   - `pedidos`
   - `detalle_pedidos`

Usuario de prueba:

- correo: `admin@test.com`
- contrasena: `1234`

## 3. Crear el archivo .env

En la raiz del proyecto crea un archivo llamado `.env`.

Puedes usar la conexion completa:

```env
DATABASE_URL=postgresql://postgres:TU_PASSWORD@db.xxxxx.supabase.co:5432/postgres?sslmode=require
```

O tambien variables separadas:

```env
DB_HOST=db.xxxxx.supabase.co
DB_PORT=5432
DB_NAME=postgres
DB_USER=postgres
DB_PASSWORD=TU_PASSWORD
```

## 4. Ejecutar el proyecto en local

Levantar el contenedor:

```bash
docker compose up --build
```

Luego abrir en el navegador:

```text
http://localhost:8080/login.php
```

## 5. Credenciales de acceso

Usar este usuario de prueba:

- email: `admin@test.com`
- password: `1234`

## 6. Estructura principal

```text
public/                 Archivos publicos del sistema
src/config/             Conexion a base de datos
src/controllers/        Controladores
src/models/             Modelos
src/views/              Vistas
database/database.sql   Script de base de datos
Dockerfile              Imagen para Docker y Render
docker-compose.yml      Ejecucion local
render.yaml             Configuracion para Render
```

## 7. Despliegue en Render

1. Subir el proyecto a `GitHub`.
2. Crear un `Web Service` en `Render`.
3. Conectar el repositorio.
4. Dejar que `Render` detecte el `Dockerfile`.
5. Agregar en `Environment` la variable:

```text
DATABASE_URL=postgresql://postgres:TU_PASSWORD@db.xxxxx.supabase.co:5432/postgres?sslmode=require
```

6. Hacer deploy.
7. Abrir la URL publica y entrar a `/login.php`.

## 8. Pruebas sugeridas

Una vez corriendo, se pueden validar estos casos:

- `TC-01`: login con credenciales validas
- `TC-02`: intento de agregar un producto con stock no suficiente
- `TC-03`: busqueda de un producto inexistente

## 9. Solucion de problemas

- Si no conecta a la base de datos, revisar `DATABASE_URL` o las variables `DB_*`.
- Si `Supabase` rechaza la conexion, usar `?sslmode=require`.
- Si el contenedor no levanta, ejecutar otra vez `docker compose up --build`.
- Si no aparecen datos, verificar que `database/database.sql` fue ejecutado correctamente.

## 10. Nota

El archivo `.env` no se sube al repositorio por seguridad. Cada integrante debe crear el suyo con sus propias credenciales o con la cadena compartida del proyecto.

# Publicar en Render con Supabase

## 1. Subir el proyecto al repositorio

Sube estos cambios a GitHub o al repositorio que tengas conectado con Render.

## 2. Preparar la base de datos en Supabase

1. Abre tu proyecto en Supabase.
2. Ve a `SQL Editor`.
3. Ejecuta el contenido de `database/database.sql`.
4. Verifica que existan estas tablas:
   - `usuarios`
   - `clientes`
   - `productos`
   - `pedidos`
   - `detalle_pedidos`

El usuario de prueba queda asi:

- Email: `admin@test.com`
- Contrasena: `1234`

## 3. Obtener la conexion de Supabase

Puedes usar cualquiera de estas opciones:

- Opcion recomendada: `DATABASE_URL`
- Opcion alternativa: `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, `DB_PASSWORD`

En Supabase normalmente encuentras esto en:

- `Project Settings`
- `Database`
- `Connection string`

Si usas `DATABASE_URL`, asegurate de copiar la cadena completa, por ejemplo:

```text
postgresql://postgres:TU_PASSWORD@db.xxxxx.supabase.co:5432/postgres?sslmode=require
```

## 4. Crear o reconfigurar el servicio en Render

1. Entra a Render.
2. Crea un nuevo `Web Service` desde tu repositorio.
3. Render detectara el `Dockerfile`.
4. Si usas Blueprint, puede leer `render.yaml`.

Configura:

- Runtime: `Docker`
- Branch: la rama donde subiste los cambios
- Auto Deploy: activado

## 5. Agregar variable de entorno

En Render, en `Environment`, agrega:

```text
DATABASE_URL=postgresql://postgres:TU_PASSWORD@db.xxxxx.supabase.co:5432/postgres?sslmode=require
```

Si prefieres variables separadas, agrega:

```text
DB_HOST=db.xxxxx.supabase.co
DB_PORT=5432
DB_NAME=postgres
DB_USER=postgres
DB_PASSWORD=TU_PASSWORD
```

## 6. Desplegar y probar

Cuando termine el deploy:

1. Abre la URL publica de Render.
2. Entra a `/login.php`.
3. Usa:
   - Email: `admin@test.com`
   - Contrasena: `1234`

## 7. Casos de prueba

Desde el sistema publicado puedes probar:

- `TC-01`: login con credenciales validas
- `TC-02`: producto sin stock suficiente
- `TC-03`: busqueda sin resultados

## 8. Si algo falla

Revisa en Render:

- `Logs`
- que `DATABASE_URL` este bien copiada
- que el script SQL se haya ejecutado en Supabase
- que el proyecto de Supabase permita la conexion usada

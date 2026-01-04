# Instalación y Ejecución

### 1. Configuración en entorno local

#### 1.1 Instalación de dependencias

```bash
# Instala las dependencias de PHP y JS:
composer install
pnpm install
# Copia el archivo de entorno y genera la clave de la aplicación:
cp .env.example .env
php artisan key:generate
```

#### 1.2 Filesystem

Se crea el enlace simbólico para poder usar de manera pública el acceso a storage/exercise-files (para este caso de pruebas)
```bash
php artisan storage:link
```

#### 1.3 Base de Datos

```bash
php artisan migrate --seed
```

#### 1.4 Ejecutar el Servidor

Levanta el servidor de desarrollo local:

```bash
php artisan serve
pnpm run build
```

El proyecto estará disponible en `http://localhost:8000`.

### 2. Usuarios de Prueba

Si se ejecutaron los seeders correctamente, puedes usar el siguiente usuario:

- **Email:** edw-toni@hotmail.com
- **Password:** hola1234

## 3. Herramientas usadas

### Frontend & Visualización
- **JS pdf image viewer**: `real3d-flipbook-jquery-plugin`
  - *Motivo*: Ofrece una mejor visualización e interacción para el usuario.
  - https://real3dflipbook.com/

### Backend & Framework
- **Laravel Filament y Livewire**
- **API**:
  - **Autenticación**: Laravel Sanctum
  - **IDs**: `cybercog/laravel-optimus` (para codificación de IDs)
  - **Documentación**: [storage/docs/search-inside-a-book.postman_collection.json](storage/docs/search-inside-a-book.postman_collection.json)

### Inteligencia Artificial (IA)
- **Perplexity**:
  - *Prompt*: "Para poder buscar coincidencias en tablas de manera eficiente en BD, lo he usado con like %term%, pero en mysql y con índices, pero ahora quiero implementarlo multi-idioma y usado POSGRESQL, cuál sería la mejor forma o alguna herramienta adicional para las consultas?"
  - *Respuesta*: Uso de `tsvector`. Ver implementación en `app/Models/BookPage.php` (L30-L37).
  
  - Una vez investigado procedí a realizar las migraciones, usé la BD postgre porque es la sugerida en la documentación y es la que tengo en ejecución al momento de hacer la prueba y para poder usar tsvector.
- **Google Antigravity**:
  - Predicción y autocompletado al escribir código.
  - Predicción para escribir las traducciones en `resources/lang`.
  - Predicción y replicación de código escrito en otros archivos, corrección de errores de manera automática.

### 4. Desafíos
- Pocos desafíos técnicos debido a experiencia previa similar (Angular).
- El visor se eligió por su facilidad de uso y su interacción.

### 5. Pruebas finales con Laravel Sail

#### 5.1 Iniciar el entorno (si no está corriendo)

```bash
composer install
./vendor/bin/sail pnpm install

# Tablas y datos
./vendor/bin/sail artisan migrate

# Enlace simbólico
./vendor/bin/sail artisan storage:link  

./vendor/bin/sail up -d
./vendor/bin/sail pnpm build
```

### 6. Datos y Almacenamiento

- **Seeders y Tablas**: Se configuraron discos en el filesystem de Laravel para gestionar la ubicación de los archivos. Esto permite indicar claramente desde dónde se llaman los archivos para su visualización.
- **Contenido de Páginas**: La población de datos para el contenido de las páginas se realizó utilizando el archivo `storage/exercise-files/Eloquent_JavaScript.json`.

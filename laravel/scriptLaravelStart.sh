#!/bin/bash

# ==============================================================================
# 🎓 SCRIPT DE INICIALIZACIÓN DE LARAVEL PARA CLASE (VÍA SAIL)
# Autor: Amador
# Descripción: Crea un entorno de desarrollo profesional usando el estándar oficial.
# ==============================================================================

# Detener el script si hay errores
set -e

# Colores para la salida
GREEN='\033[0;32m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

# 1. VERIFICACIÓN INICIAL
# ==============================================================================
if [ -z "$1" ]; then
    echo -e "${RED}❌ Error: Falta el nombre del proyecto.${NC}"
    echo -e "${YELLOW}Uso correcto: ./crear-proyecto-clase.sh nombre-proyecto${NC}"
    exit 1
fi

PROJECT_NAME=$1

# Verificar si Docker está corriendo
if ! docker info > /dev/null 2>&1; then
    echo -e "${RED}❌ Error: Docker no está corriendo.${NC}"
    echo -e "Por favor, inicia Docker Desktop antes de continuar."
    exit 1
fi

echo -e "${BLUE}====================================================${NC}"
echo -e "${BLUE} 🚀 INICIANDO SETUP DE LARAVEL: ${YELLOW}$PROJECT_NAME${NC}"
echo -e "${BLUE}====================================================${NC}\n"

# 2. DESCARGA E INSTALACIÓN (MÉTODO SAIL)
# ==============================================================================
# Usamos el instalador oficial de Laravel Build.
# Solicitamos mysql, redis y mailpit (para ver emails falsos en local).
echo -e "${GREEN}📦 Paso 1: Descargando Laravel y configurando contenedores...${NC}"
curl -s "https://laravel.build/$PROJECT_NAME?with=mysql,redis,mailpit" | bash

cd "$PROJECT_NAME"

# 3. PERSONALIZACIÓN DEL ENTORNO DOCKER
# ==============================================================================
echo -e "\n${GREEN}🔧 Paso 2: Vitaminando el docker-compose.yml...${NC}"

# Añadimos phpMyAdmin al docker-compose.yml automáticamente.
# Es una herramienta visual útil para estudiantes.
cat >> docker-compose.yml << EOF

    # Añadido por script de clase: phpMyAdmin
    phpmyadmin:
        image: phpmyadmin/phpmyadmin
        links:
            - mysql:mysql
        ports:
            - 8080:80
        environment:
            PMA_HOST: mysql
            MYSQL_ROOT_PASSWORD: '\${DB_PASSWORD}'
        networks:
            - sail
EOF

# 4. LEVANTAR EL ENTORNO
# ==============================================================================
echo -e "\n${GREEN}🐳 Paso 3: Levantando los contenedores (esto puede tardar la primera vez)...${NC}"
./vendor/bin/sail up -d

# 5. INSTALACIÓN DE HERRAMIENTAS EDUCATIVAS
# ==============================================================================
echo -e "\n${GREEN}📚 Paso 4: Instalando herramientas para desarrollo...${NC}"

# A) Laravel Debugbar: Fundamental para enseñar optimización y ver queries SQL.
echo -e "   Running: ${YELLOW}composer require barryvdh/laravel-debugbar --dev${NC}"
./vendor/bin/sail composer require barryvdh/laravel-debugbar --dev

# B) Laravel Lang: Para que los mensajes de error salgan en español.
echo -e "   Running: ${YELLOW}composer require laravel-lang/common --dev${NC}"
./vendor/bin/sail composer require laravel-lang/common --dev
# Publicar traducciones
./vendor/bin/sail artisan lang:publish
# Configurar español en config/app.php y .env
sed -i '' "s/APP_LOCALE=en/APP_LOCALE=es/" .env 2>/dev/null || sed -i "s/APP_LOCALE=en/APP_LOCALE=es/" .env

# 6. GENERACIÓN DE ALIAS (HELPERS)
# ==============================================================================
echo -e "\n${GREEN}⚡ Paso 5: Creando atajos de teclado...${NC}"

cat > sail-alias.sh << 'EOF'
#!/bin/bash
# Carga este archivo con: source sail-alias.sh

# El comando mágico 'sail'
alias sail='[ -f sail ] && bash sail || bash vendor/bin/sail'

# Atajos rápidos
alias sup='sail up -d'
alias sdown='sail stop'
alias art='sail artisan'
alias mig='sail artisan migrate'
alias fresh='sail artisan migrate:fresh --seed'
alias t='sail test'
alias composer='sail composer'
alias npm='sail npm'
EOF

chmod +x sail-alias.sh

# 7. RESUMEN FINAL
# ==============================================================================
echo -e "\n${BLUE}====================================================${NC}"
echo -e "${GREEN}✅ ¡PROYECTO LISTO PARA CLASE!${NC}"
echo -e "${BLUE}====================================================${NC}\n"

echo -e "📂 Ubicación: $(pwd)"
echo -e "🌐 URLs de Acceso:"
echo -e "   • App Principal:  ${YELLOW}http://localhost${NC}"
echo -e "   • phpMyAdmin:     ${YELLOW}http://localhost:8080${NC} (User: sail / Pass: password)"
echo -e "   • Correos (Mailpit): ${YELLOW}http://localhost:8025${NC}"

echo -e "\n💡 PASOS PARA EL ALUMNO:"
echo -e "1. Entra al directorio:   cd $PROJECT_NAME"
echo -e "2. Activa los atajos:     source sail-alias.sh"
echo -e "3. Inicia compilación JS: sail npm install && sail npm run dev"
echo -e "4. ¡A programar!"

echo -e "\n${YELLOW}Nota para usuarios Mac:${NC} Si usas Docker Desktop, asegura activar 'VirtioFS' en settings."

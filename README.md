# Sistema de Gerenciamento de Produtos

Com Laravel 11, Livewire 3 e Tailwind CSS.

### Clone o repositório
```bash
git clone https://github.com/caiquebispo/products-app.git
cd products-app
```

### Instale as dependências PHP
```bash
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php82-composer:latest \
    composer install --ignore-platform-reqs
```

### Configure o ambiente
```bash
cp .env.example .env
```

### Inicie os containers Docker
```bash
./vendor/bin/sail up -d
```

### Gere a chave da aplicação
```bash
./vendor/bin/sail artisan key:generate
```

### 7. Execute as migrações
```bash
./vendor/bin/sail artisan migrate --seed
```

### 8. Instale as dependências Node.js
```bash
./vendor/bin/sail npm install
```

### 9. Compile os assets
```bash
./vendor/bin/sail npm run build
```

### 10. Compile os assets
```bash
./vendor/bin/sail composer dum-autoload
```

Abra seu navegador e acesse: http://localhost

email: test@example.com
password: password

### Gerenciamento de containers
```bash
# Iniciar containers
./vendor/bin/sail up -d

# Parar containers
./vendor/bin/sail down

# Reiniciar containers
./vendor/bin/sail restart
```

### Comandos Artisan
```bash
# Executar migrações
./vendor/bin/sail artisan migrate

# Executar seeders
./vendor/bin/sail artisan db:seed

# Limpar cache
./vendor/bin/sail artisan cache:clear

# Gerar chave da aplicação
./vendor/bin/sail artisan key:generate
```

### Acesso ao container
```bash
# Acessar bash do container
./vendor/bin/sail bash

# Executar comandos PHP
./vendor/bin/sail php -v

# Acessar MySQL
./vendor/bin/sail mysql
```

## Testes

### Executar todos os testes
```bash
./vendor/bin/sail test
```

### Executar testes específicos
```bash
# Testes de produtos
./vendor/bin/sail test tests/Feature/Livewire/Products/

# Testes de categorias
./vendor/bin/sail test tests/Feature/Livewire/Categories/

# Testes de marcas
./vendor/bin/sail test tests/Feature/Livewire/Brands/
```

### Preparação para produção
```bash
# Otimizar autoloader
./vendor/bin/sail composer install --optimize-autoloader --no-dev

# Compilar assets para produção
./vendor/bin/sail npm run build

# Otimizar configuração
./vendor/bin/sail artisan config:cache
./vendor/bin/sail artisan route:cache
./vendor/bin/sail artisan view:cache
```


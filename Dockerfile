FROM php:apache

# Atualizando e instalando dependências
RUN apt update && \
    apt install -y libpq-dev && \
    apt install -y autoconf -y && \
    pecl install xdebug && \
    docker-php-ext-enable xdebug && \
    docker-php-ext-install pdo pdo_pgsql

# Ativando o módulo rewrite do Apache
RUN a2enmod rewrite

# Copiando arquivos de configuração
COPY xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

# Configurando o Apache para reconhecer o index.php como o arquivo padrão
COPY 000-default.conf /etc/apache2/sites-available/000-default.conf
RUN sed -i 's/DirectoryIndex /DirectoryIndex index.php /' /etc/apache2/apache2.conf

# Reiniciando o Apache para aplicar as configurações
RUN service apache2 restart

# Copiando o conteúdo do aplicativo para o diretório do Apache
COPY . /var/www/html/
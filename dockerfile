# Use uma versão mais recente e leve
FROM nginx:1.27-alpine

# Remova a configuração padrão para evitar conflitos
RUN rm -f /etc/nginx/conf.d/default.conf

# Copie sua configuração principal
COPY nginx.conf /etc/nginx/nginx.conf

# (Opcional) Copie configs adicionais
# COPY conf.d/*.conf /etc/nginx/conf.d/

# Ajuste permissões (boa prática em ambientes corporativos)
RUN chmod 644 /etc/nginx/nginx.conf

# Exponha a porta usada pelo Nginx
EXPOSE 80
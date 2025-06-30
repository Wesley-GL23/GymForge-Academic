# Configuração do Cloudflare CDN para GYMFORGE™

Este guia vai te ajudar a configurar um CDN gratuito para os vídeos do GYMFORGE™.

## 1. Criar Conta no Cloudflare

1. Acesse [Cloudflare.com](https://cloudflare.com)
2. Clique em "Sign Up"
3. Use seu email e crie uma senha

## 2. Adicionar Domínio (Gratuito)

1. No painel do Cloudflare, clique em "Add a Site"
2. Digite seu domínio (ex: gymforge.com.br)
3. Selecione o plano "Free"
4. Siga as instruções para configurar os nameservers

## 3. Configurar Page Rules

1. No painel do seu site, vá em "Page Rules"
2. Adicione uma nova regra:
   - URL: `*/assets/videos/*`
   - Configurações:
     - Cache Level: Cache Everything
     - Edge Cache TTL: 1 month
     - Browser Cache TTL: 1 month

## 4. Configurar Workers (Opcional, mas recomendado)

1. Vá em "Workers"
2. Crie um novo Worker
3. Use o código abaixo para otimizar a entrega dos vídeos:

```js
addEventListener('fetch', event => {
  event.respondWith(handleRequest(event.request))
})

async function handleRequest(request) {
  // Adicionar headers de CORS
  const corsHeaders = {
    'Access-Control-Allow-Origin': '*',
    'Access-Control-Allow-Methods': 'GET, HEAD, OPTIONS',
    'Access-Control-Max-Age': '86400',
  }

  // Clonar a resposta original e adicionar headers
  const response = await fetch(request)
  const newResponse = new Response(response.body, response)
  
  // Adicionar headers de cache e CORS
  newResponse.headers.set('Cache-Control', 'public, max-age=31536000')
  Object.keys(corsHeaders).forEach(key => {
    newResponse.headers.set(key, corsHeaders[key])
  })

  return newResponse
}
```

## 5. Atualizar Configuração do GYMFORGE™

1. Edite o arquivo `config/cdn_config.php`
2. Atualize a constante `CDN_URL` com seu domínio:
```php
define('CDN_URL', 'https://seu-dominio.com.br');
```

## 6. Testar

1. Faça upload de um vídeo de teste
2. Verifique se o CDN está funcionando acessando:
   `https://seu-dominio.com.br/assets/videos/previews/categoria/video.webm`
3. Use as ferramentas de desenvolvedor do navegador para verificar se:
   - O vídeo está sendo servido pelo Cloudflare
   - Os headers de cache estão corretos
   - O CORS está funcionando

## Benefícios

- CDN gratuito global
- Economia de banda
- Melhor performance
- Proteção DDoS
- Cache automático

## Suporte

Se precisar de ajuda, consulte:
- [Documentação Cloudflare](https://developers.cloudflare.com/)
- [Fórum da Comunidade](https://community.cloudflare.com/)
- Ou abra uma issue no repositório do GYMFORGE™ 
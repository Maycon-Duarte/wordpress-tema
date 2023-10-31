
# wordpress-tema

Este repositório, intitulado "wordpress-tema," é um projeto de tema para WordPress que apresenta uma estrutura de classes organizada para o desenvolvimento de sites com WordPress.

## Instalação

clone o projeto na pasta de temas do wordpress e instale as dependências usando:

```bash
npm install
```
    
## WP_CLI

O *WP_CLI* é a interface de linha de comando do WordPress, e nesse projeto permite além dos comandos comuns execultar o comando de criar widget

antes de começar será interresante instalar o *WP_CLI* localmente:

- [Como instalar o WP_CLI](https://wp-cli.org/#installing)

### Criando widgets com WP_CLI

A classe *ElementorWidgets* é responsável por importar os widgets personalizados do tema junto com suas dependencias de css e js, mas também é usada para remover os widgets padrões do elementor que não são acessíveis e traz o comando:

```bash
wp create-widget <nome-do-widget>
```

Esse comando gera um arquivo modelo para começar o desenvolvimento de um novo widget, além de gerar os arquivos de suas dependências de css e js 

- [Entendendo dependências de widget](https://developers.elementor.com/docs/widgets/widget-dependencies/)
- [Documentação dos custom widgets](https://developers.elementor.com/docs/widgets/)
- [Documentação do Elementor](https://developers.elementor.com/)

## Uso do Gulp

Antes de continuar, é importante entender a estrutura da pasta assets do projeto:


- `assets/scss/`: Diretório contendo arquivos Sass que serão compilados em CSS.
- `assets/css`: Díretório com o CSS já compilado
- `assets/js/`: Diretório contendo arquivos JavaScript.
- `gulpfile.js`: O arquivo de configuração do Gulp com as tarefas definidas.

**Atenção**: O gulp ignora os arquivos com underline no começo e somente o arquivo principal `style.scss` e os arquivos dentro da pasta `assets/scss/widgets` devem começar sem o underliner para que sejam gerados na pasta `assets/css`, os outros devem ser importados dentro deles, ou caso n queira usar os recursos de dependencias do elementor pode importar tudo em style.scss e adicionar um underline nos arquivos de `assets/scss/widgets`

### Tarefas Gulp Disponíveis

Este arquivo `gulpfile.js` contém as seguintes tarefas Gulp:

- **sass**: Esta tarefa compila os arquivos Sass localizados em `assets/scss/` para arquivos CSS e os armazena em `assets/css/`.

- **scripts**: Esta tarefa processa o arquivo JavaScript `assets/js/scripts.js`, que pode incluir outros arquivos JavaScript usando `//= require`, e os concatena em um único arquivo, que é armazenado na raiz do projeto.

- **watch**: Esta tarefa observa as mudanças nos arquivos `.scss` em `assets/scss/` e nos arquivos `.js` em `assets/js/`. Sempre que uma mudança é detectada, as tarefas `sass` e `scripts` correspondentes são acionadas automaticamente.

- **default**: Esta tarefa é a tarefa padrão do Gulp. Ao executar `gulp` no terminal, as tarefas `sass`, `scripts` e `watch` serão executadas automaticamente em sequência, tornando o processo de desenvolvimento mais eficiente.

## Recursos SCSS

Alguns arquivos scss foram adicionados com algumas funções para facilitar e padronizar a forma como o CSS é estruturado, confira alguns recursos:

### _functions.scss

O `#{dark-mode-selector('&')}` Adiciona facilmente os atributos css para o dark mode dentro ou fora do seletor css

Exemplo de uso dentro:

```bash
body {
    // outros atributos aqui...
    
    #{dark-mode-selector('&')} {
        background-color: black;
    }
}
```

Exemplo de uso fora:

```bash
#{dark-mode-selector('body')} {
    background-color: black;
}

```

### _variables.scss

Aqui você pode ser usado para definir variaveis como cores e fontes do site

Exemplo:

```bash
// PT Serif
@import url('https://fonts.googleapis.com/css2?family=PT+Serif:ital,wght@0,400;0,700;1,400;1,700&display=swap');
$font-pt-serif: 'PT Serif', serif;

// Cores
$preto: #000000;
$branco: #FFFFFF;

```

### _mixins.scss

Aqui os breakpoins do css já são definidos, mas pode ser usado para padronizar um conjunto de propriedades css comuns 

Exemplo:

```bash
@mixin tablet {
    @media(max-width: 769px) {
        @content;
    }
}

// uso 
body {  
    // outros atributos aqui...

    @include mixins.tablet {
        margin: 10px;
    }
}
```


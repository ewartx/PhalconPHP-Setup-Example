module.exports = (grunt)->

  ###
    Define Default Paths
  ###

  globalConfig = 
    path: "public/assets"
    srcPath: "amplus/assets"
    jsVendorPath: "public/assets/js/vendor"
    cssVendorPath: "public/assets/css/vendor"
    layoutFile: "app/views/index.volt"
    httpPath: "http://amplusmarketing.com" # url to your live site

  ###
    Setting up CSS & JS Load Order
  ###

  cssFilesToInject = [
    globalConfig.cssVendorPath + "/bootstrap.min.css"
    globalConfig.cssVendorPath + '/**/*.css'
    globalConfig.path + '/css/**/*.css'
  ]

  jsFilesToInject = [
    globalConfig.jsVendorPath + '/jquery.min.js'
    globalConfig.jsVendorPath + '/bootstrap.min.js'
  ]

  ###
    Grunt Initial Settings
  ###
  grunt.initConfig
    data: globalConfig
    pkg: grunt.file.readJSON('package.json')

    # Setting up Concurrent Operations
    concurrent:
      prepareDevLinks: ['sails-linker:devJs', 'sails-linker:devStyles']      
      prepareProdLinks: ['sails-linker:prodJs', 'sails-linker:prodStyles']
      preprocessors: ['newer:coffee:dev', 'newer:imagemin:dist']
      "prod-preprocessors": ["compass:dist"]
      minify: ['newer:uglify:dist', 'newer:cssmin:dist']
      watch:
        tasks: ['watch', 'compass:dev']
        options:
          logConcurrentOutput: true

    ###
      Development Specific Settings
    ###   
    # CoffeeScript
    coffee:
      dev:
        options:
          bare: true
          sourceMaps: true
          sourceMapDir: '<%= data.path %>/js/app/sourcemaps/'
        files: [
          expand: true
          cwd: '<%= data.path %>/js/app/coffeescripts/'
          src: ['**/*.coffee']
          dest: '<%= data.path %>/js/app/'
          ext: '.js'
        ]

    # Compass (includes Sass)
    compass:
      dev: # default environment is development
        options:
          sassDir: '<%= data.path %>/css/app/sass'
          cssDir:  '<%= data.path %>/css/app'
          imagesDir:  '<%= data.path %>/img/src'
          fontsDir:  '<%= data.srcPath %>/fonts'
          generatedImagesPath: '<%= data.path %>/img/src'
          watch: true
      dist:
        options:
          relativeAssets: false
          httpPath: '<%= data.httpPath %>'    
          httpStylesheetsPath: '<%= data.httpPath %>/css/app'
          httpImagesPath: '<%= data.httpPath %>/img/dist'
          httpGeneratedImagesPath: '<%= data.httpPath %>/img/dist'
          httpJavascriptsPath: '<%= data.httpPath %>/js/app'
          httpFontsPath: '<%= data.httpPath %>/fonts'
          environment: 'production'

    # CoffeeScript JSLint Plugin
    coffeelint:     
      app: ['<%= data.path %>/js/app/**/*.coffee']

    # Clean (Delete js/css files after the .coffee/.sass is Deleted)
    clean:
      js:
        src: ['<%= data.path %>/js/app/filename']
      css:
        src: ['<%= data.path %>/css/app/filename']
      img:
        src: ['<%= data.path %>/img/src/filename']

    # Image Optimization
    imagemin:
      dist:
        files: [
          expand: true
          cwd: '<%= data.path %>/img/src'
          src: ['**/*.{png,jpg,gif}']
          dest: '<%= data.path %>/img/dist'
        ]

    ###
      Production Specific Settings
    ###   

    # Concatenate the Compiled CSS and JS Files
    concat:
      js:
        src: jsFilesToInject
        dest: '<%= data.httpPath %>/js/production.js'
      css:
        src: cssFilesToInject
        dest: '<%= data.httpPath %>/css/production.css'

    # Compress the JS Files
    uglify:
      dist:
        files:
          '<%= data.httpPath %>/js/production.min.js': ['<%= data.httpPath %>/js/production.js']

    # Compress the CSS Files
    cssmin:
      dist:
        files:
          '<%= data.httpPath %>/css/production.min.css': ['<%= data.httpPath %>/css/production.css']

    ###
      Linking up the Files
    ### 

    # Injecting Links for CSS and JS into Layout Page
    'sails-linker':
      devJs:
        options:
          startTag: '<!--SCRIPTS-->'
          endTag: '<!--SCRIPTS END-->'
          fileTmpl: '<script src="%s"></script>'
          appRoot: "public/"
        files:
          '<%= data.layoutFile %>': jsFilesToInject
      prodJs:
        options:
          startTag: '<!--SCRIPTS-->'
          endTag: '<!--SCRIPTS END-->'
          fileTmpl: '<script src="%s?ver=<%= pkg.version %>"></script>'
          appRoot: "public/"
        files:
          '<%= data.layoutFile %>': ['<%= data.path %>/js/production.min.js']
      devStyles:
        options:
          startTag: '<!--STYLES-->'
          endTag: '<!--STYLES END-->'
          fileTmpl: '<link rel="stylesheet" href="%s" />'
          appRoot: "public/"
        files:
            '<%= data.layoutFile %>': cssFilesToInject

    # Adding Watch Options
    watch:
      configFiles:
        files: ['Gruntfile.coffee']
        options:
          reload: true,
          livereload: true
      # Auto Linking on Delete/Add
      css:
        files: ['<%= data.path %>/css/**/*.css']
        tasks: ['sails-linker:devStyles']
        options:
          event: ['added', 'deleted']
      js:
        files: ['<%= data.path %>/js/**/*.js']
        tasks: ['sails-linker:devJs']
        options:
          event: ['added', 'deleted']
      coffeeScripts:
        files: ['<%= data.path %>/js/app/coffeescripts/**/*.coffee']
        tasks: ['newer:coffee:dev', 'coffeelint']
        options:
          event: ['added', 'changed']
      imageAdd:
        files: ['<%= data.path %>/img/src/**/*.{png,jpg,gif}']
        tasks: ['newer:imagemin:dist']
        options:
          event: ['added']
      # Deletion of CoffeeScripts, Sass Scripts, Images
      deleteCoffeeScripts:
        files: ['<%= data.path %>/js/app/coffeescripts/**/*.coffee']
        tasks: ['clean:js']
        options:
          event: ['deleted']
          spawn: false
      deleteSass:
        files: ['<%= data.path %>/css/app/sass/**/*.sass']
        tasks: ['clean:css']
        options:
          event: ['deleted']
          spawn: false
      imageDelete:
        files: ['<%= data.path %>/img/src/**/*']
        tasks: ['clean:img']
        options:
          event: ['deleted']
          spawn: false

  ###
    Running Tasks
  ###

  require("load-grunt-tasks")(grunt)

  # Custom Task Response
  grunt.event.on "watch", (action, filepath, target)->
    if (target is 'deleteCoffeeScripts' and action is 'deleted')
      # Finds the JS Equivalent Path
      javascriptPath = (filepath.replace("coffeescripts\\", "")).replace(".coffee", ".js")
      grunt.config.set("clean.js.src", javascriptPath)
    if  (target is 'deleteSass' and action is 'deleted')
      # Finds the CSS Equivalent Path
      cssPath = (filepath.replace("sass\\", "")).replace(".sass", ".css")
      grunt.config("clean.css.src", cssPath)
    if (target is "imageDelete" and action is 'deleted')
      imgPath = filepath.replace("src\\", "dist\\")
      grunt.config("clean.img.src", imgPath)

  # Aliasing Tasks - Sectioning Tasks for Better Management
  grunt.registerTask "initializeApp", [
    'concurrent:preprocessors'
    'concurrent:prepareDevLinks'
    'coffeelint'
  ]

  grunt.registerTask "initializeProdApp", [
    'concurrent: prod-preprocessors'
    'concurrent: minify'
    'concurrent: prepareProdLinks'
  ]

  # Default Task
  grunt.registerTask "default", [
    'initializeApp'
    "concurrent:watch"  
  ]

  # Production Environment
  grunt.registerTask "prod", [
    'initializeProdApp'
  ]
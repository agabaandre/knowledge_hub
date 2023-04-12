var gulp = require('gulp');
sass = require("gulp-sass"),
postcss = require("gulp-postcss");
autoprefixer = require("autoprefixer");
var sourcemaps = require('gulp-sourcemaps'); 
var browserSync = require('browser-sync').create(); 
cssbeautify = require('gulp-cssbeautify');
var beautify = require('gulp-beautify');


//_______ task for scss folder to css main style 
gulp.task('watch', function() {
	console.log('Command executed successfully compiling SCSS in assets.');
	 return gulp.src('Admix/assets/scss/**/*.scss') 
		.pipe(sourcemaps.init())                       
		.pipe(sass())
		.pipe(sourcemaps.write(''))   
		.pipe(gulp.dest('Admix/assets/css'))
		.pipe(browserSync.reload({
		  stream: true
	}))
})

//_______task for style-dark
gulp.task('dark', function(){
   return gulp.src('Admix/assets/css/style-dark.scss')
        .pipe(sourcemaps.init())
        .pipe(sass())
        .pipe(sourcemaps.write('.'))
        .pipe(gulp.dest('Admix/assets/css'));
		
});


//_______task for leftmenu
gulp.task('menu', function(){
	return gulp.src('Admix/assets/css/sidemenu/sidemenu.scss')
		 .pipe(sourcemaps.init())
		 .pipe(sass())
		 .pipe(sourcemaps.write('.'))
		 .pipe(gulp.dest('Admix/assets/css/sidemenu'));
		 
 });
 
//_______task for skinmodes
gulp.task('skin', function(){
	return gulp.src('Admix/assets/css/skin-modes.scss')
		 .pipe(sourcemaps.init())
		 .pipe(sass())
		 .pipe(sourcemaps.write('.'))
		 .pipe(gulp.dest('Admix/assets/css'));
		 
 });
 

//_______task for doublemenu tabs
gulp.task('res-tabs', function(){
	return gulp.src('Admix/assets/css/sidemenu/sidemenu-responsive-tabs.scss')
		 .pipe(sourcemaps.init())
		 .pipe(sass())
		 .pipe(sourcemaps.write('.'))
		 .pipe(gulp.dest('Admix/assets/css/sidemenu'));
		 
 });

//_______task for Horizontalmenu
gulp.task('horizontal', function(){
	return gulp.src('Admix/assets/css/horizontalmenu/horizontal-menu.scss')
		 .pipe(sourcemaps.init())
		 .pipe(sass())
		 .pipe(sourcemaps.write('.'))
		 .pipe(gulp.dest('Admix/assets/css/horizontalmenu'));
		 
 });
 
 
//_______task for Singlemenu
gulp.task('singlemenu', function(){
	return gulp.src('Admix/assets/css/single-sidemenu/singlemenu.scss')
		 .pipe(sourcemaps.init())
		 .pipe(sass())
		 .pipe(sourcemaps.write('.'))
		 .pipe(gulp.dest('Admix/assets/css/single-sidemenu'));
		 
});

//_______task for Singlemenu
gulp.task('singlemenu2', function(){
	return gulp.src('Admix/assets/css/single-sidemenu/singlemenu2.scss')
		 .pipe(sourcemaps.init())
		 .pipe(sass())
		 .pipe(sourcemaps.write('.'))
		 .pipe(gulp.dest('Admix/assets/css/single-sidemenu'));
		 
});


//_______task for Color
gulp.task('color', function(){
	return gulp.src('Admix/assets/css/colors/color.scss')
		 .pipe(sourcemaps.init())
		 .pipe(sass())
		 .pipe(sourcemaps.write('.'))
		 .pipe(gulp.dest('Admix/assets/css/colors'));
		 
});
//_______task for Color1
gulp.task('color1', function(){
	return gulp.src('Admix/assets/css/colors/color1.scss')
		 .pipe(sourcemaps.init())
		 .pipe(sass())
		 .pipe(sourcemaps.write('.'))
		 .pipe(gulp.dest('Admix/assets/css/colors'));
		 
});
//_______task for Color2
gulp.task('color2', function(){
	return gulp.src('Admix/assets/css/colors/color2.scss')
		 .pipe(sourcemaps.init())
		 .pipe(sass())
		 .pipe(sourcemaps.write('.'))
		 .pipe(gulp.dest('Admix/assets/css/colors'));
		 
});
//_______task for Color3
gulp.task('color3', function(){
	return gulp.src('Admix/assets/css/colors/color3.scss')
		 .pipe(sourcemaps.init())
		 .pipe(sass())
		 .pipe(sourcemaps.write('.'))
		 .pipe(gulp.dest('Admix/assets/css/colors'));
		 
});
//_______task for Color4
gulp.task('color4', function(){
	return gulp.src('Admix/assets/css/colors/color4.scss')
		 .pipe(sourcemaps.init())
		 .pipe(sass())
		 .pipe(sourcemaps.write('.'))
		 .pipe(gulp.dest('Admix/assets/css/colors'));
		 
});
//_______task for Color5
gulp.task('color5', function(){
	return gulp.src('Admix/assets/css/colors/color5.scss')
		 .pipe(sourcemaps.init())
		 .pipe(sass())
		 .pipe(sourcemaps.write('.'))
		 .pipe(gulp.dest('Admix/assets/css/colors'));
		 
});



//_______task for style-dark
gulp.task('boxed', function(){
   return gulp.src('Admix/assets/css/boxed.scss')
        .pipe(sourcemaps.init())
        .pipe(sass())
        .pipe(sourcemaps.write('.'))
        .pipe(gulp.dest('Admix/assets/css'));
		
});

//_______task for style-dark
gulp.task('dark-boxed', function(){
   return gulp.src('Admix/assets/css/dark-boxed.scss')
        .pipe(sourcemaps.init())
        .pipe(sass())
        .pipe(sourcemaps.write('.'))
        .pipe(gulp.dest('Admix/assets/css'));
		
});


//_______task for Singlemenu
gulp.task('singlemenu-closed', function(){
	return gulp.src('Admix/assets/css/single-sidemenu/singlemenu-closed.scss')
		 .pipe(sourcemaps.init())
		 .pipe(sass())
		 .pipe(sourcemaps.write('.'))
		 .pipe(gulp.dest('Admix/assets/css/single-sidemenu'));
		 
});


//_______task for Singlemenu
gulp.task('singlemenu4', function(){
	return gulp.src('Admix/assets/css/single-sidemenu/singlemenu4.scss')
		 .pipe(sourcemaps.init())
		 .pipe(sass())
		 .pipe(sourcemaps.write('.'))
		 .pipe(gulp.dest('Admix/assets/css/single-sidemenu'));
		 
});

//_______task for Singlemenu
gulp.task('singlemenu3', function(){
	return gulp.src('Admix/assets/css/single-sidemenu/singlemenu3.scss')
		 .pipe(sourcemaps.init())
		 .pipe(sass())
		 .pipe(sourcemaps.write('.'))
		 .pipe(gulp.dest('Admix/assets/css/single-sidemenu'));
		 
})
/*******************  LTR-Beautify  ******************/

//_______ task for beautifying css
gulp.task('beautify', function() {
    return gulp.src('Admix/assets/css/*.css')
        .pipe(beautify.css({ indent_size: 4 }))
        .pipe(gulp.dest('Admix/assets/css'));
});




//_______ task for scss folder to css main style 
gulp.task('watch-rtl', function() {
	console.log('Command executed successfully compiling SCSS in assets.');
	 return gulp.src('Admix/assets/scss-rtl/**/*.scss') 
		.pipe(sourcemaps.init())                       
		.pipe(sass())
		.pipe(sourcemaps.write(''))   
		.pipe(gulp.dest('Admix/assets/css-rtl'))
		.pipe(browserSync.reload({
		  stream: true
	}))
})

//_______task for style-dark
gulp.task('dark-rtl', function(){
   return gulp.src('Admix/assets/css-rtl/style-dark.scss')
        .pipe(sourcemaps.init())
        .pipe(sass())
        .pipe(sourcemaps.write('.'))
        .pipe(gulp.dest('Admix/assets/css-rtl'));
		
});


//_______task for leftmenu
gulp.task('menu-rtl', function(){
	return gulp.src('Admix/assets/css-rtl/sidemenu/sidemenu.scss')
		 .pipe(sourcemaps.init())
		 .pipe(sass())
		 .pipe(sourcemaps.write('.'))
		 .pipe(gulp.dest('Admix/assets/css-rtl/sidemenu'));
		 
 });
 
//_______task for skinmodes
gulp.task('skin-rtl', function(){
	return gulp.src('Admix/assets/css-rtl/skin-modes.scss')
		 .pipe(sourcemaps.init())
		 .pipe(sass())
		 .pipe(sourcemaps.write('.'))
		 .pipe(gulp.dest('Admix/assets/css-rtl'));
		 
 });

//_______task for doublemenu tabs
gulp.task('res-tabs-rtl', function(){
	return gulp.src('Admix/assets/css-rtl/sidemenu/sidemenu-responsive-tabs.scss')
		 .pipe(sourcemaps.init())
		 .pipe(sass())
		 .pipe(sourcemaps.write('.'))
		 .pipe(gulp.dest('Admix/assets/css-rtl/sidemenu'));
		 
 });

//_______task for Horizontalmenu
gulp.task('horizontal-rtl', function(){
	return gulp.src('Admix/assets/css-rtl/horizontalmenu/horizontal-menu.scss')
		 .pipe(sourcemaps.init())
		 .pipe(sass())
		 .pipe(sourcemaps.write('.'))
		 .pipe(gulp.dest('Admix/assets/css-rtl/horizontalmenu'));
		 
 });
 
 
//_______task for Singlemenu
gulp.task('singlemenu-rtl', function(){
	return gulp.src('Admix/assets/css-rtl/single-sidemenu/singlemenu.scss')
		 .pipe(sourcemaps.init())
		 .pipe(sass())
		 .pipe(sourcemaps.write('.'))
		 .pipe(gulp.dest('Admix/assets/css-rtl/single-sidemenu'));
		 
});

//_______task for Singlemenu
gulp.task('singlemenu2-rtl', function(){
	return gulp.src('Admix/assets/css-rtl/single-sidemenu/singlemenu2.scss')
		 .pipe(sourcemaps.init())
		 .pipe(sass())
		 .pipe(sourcemaps.write('.'))
		 .pipe(gulp.dest('Admix/assets/css-rtl/single-sidemenu'));
		 
});

//_______task for Singlemenu
gulp.task('singlemenu3-rtl', function(){
	return gulp.src('Admix/assets/css-rtl/single-sidemenu/singlemenu3.scss')
		 .pipe(sourcemaps.init())
		 .pipe(sass())
		 .pipe(sourcemaps.write('.'))
		 .pipe(gulp.dest('Admix/assets/css-rtl/single-sidemenu'));
		 
});

//_______task for Singlemenu
gulp.task('singlemenu4-rtl', function(){
	return gulp.src('Admix/assets/css-rtl/single-sidemenu/singlemenu4.scss')
		 .pipe(sourcemaps.init())
		 .pipe(sass())
		 .pipe(sourcemaps.write('.'))
		 .pipe(gulp.dest('Admix/assets/css-rtl/single-sidemenu'));
		 
});
//_______task for Color
gulp.task('color-rtl', function(){
	return gulp.src('Admix/assets/css-rtl/colors/color.scss')
		 .pipe(sourcemaps.init())
		 .pipe(sass())
		 .pipe(sourcemaps.write('.'))
		 .pipe(gulp.dest('Admix/assets/css-rtl/colors'));
		 
});
//_______task for Color1
gulp.task('color1-rtl', function(){
	return gulp.src('Admix/assets/css-rtl/colors/color1.scss')
		 .pipe(sourcemaps.init())
		 .pipe(sass())
		 .pipe(sourcemaps.write('.'))
		 .pipe(gulp.dest('Admix/assets/css-rtl/colors'));
		 
});
//_______task for Color2
gulp.task('color2-rtl', function(){
	return gulp.src('Admix/assets/css-rtl/colors/color2.scss')
		 .pipe(sourcemaps.init())
		 .pipe(sass())
		 .pipe(sourcemaps.write('.'))
		 .pipe(gulp.dest('Admix/assets/css-rtl/colors'));
		 
});
//_______task for Color3
gulp.task('color3-rtl', function(){
	return gulp.src('Admix/assets/css-rtl/colors/color3.scss')
		 .pipe(sourcemaps.init())
		 .pipe(sass())
		 .pipe(sourcemaps.write('.'))
		 .pipe(gulp.dest('Admix/assets/css-rtl/colors'));
		 
});
//_______task for Color4
gulp.task('color4-rtl', function(){
	return gulp.src('Admix/assets/css-rtl/colors/color4.scss')
		 .pipe(sourcemaps.init())
		 .pipe(sass())
		 .pipe(sourcemaps.write('.'))
		 .pipe(gulp.dest('Admix/assets/css-rtl/colors'));
		 
});
//_______task for Color5
gulp.task('color5-rtl', function(){
	return gulp.src('Admix/assets/css-rtl/colors/color5.scss')
		 .pipe(sourcemaps.init())
		 .pipe(sass())
		 .pipe(sourcemaps.write('.'))
		 .pipe(gulp.dest('Admix/assets/css-rtl/colors'));
		 
});

//_______task for skinmodes
gulp.task('boxed-rtl', function(){
	return gulp.src('Admix/assets/css-rtl/boxed.scss')
		 .pipe(sourcemaps.init())
		 .pipe(sass())
		 .pipe(sourcemaps.write('.'))
		 .pipe(gulp.dest('Admix/assets/css-rtl'));
		 
 });



//_______task for skinmodes
gulp.task('dark-boxed-rtl', function(){
	return gulp.src('Admix/assets/css-rtl/dark-boxed.scss')
		 .pipe(sourcemaps.init())
		 .pipe(sass())
		 .pipe(sourcemaps.write('.'))
		 .pipe(gulp.dest('Admix/assets/css-rtl'));
		 
 });



//_______task for Singlemenu
gulp.task('singlemenu-closed-rtl', function(){
	return gulp.src('Admix/assets/css-rtl/single-sidemenu/singlemenu-closed.scss')
		 .pipe(sourcemaps.init())
		 .pipe(sass())
		 .pipe(sourcemaps.write('.'))
		 .pipe(gulp.dest('Admix/assets/css-rtl/single-sidemenu'));
		 
});
/*******************  LTR-Beautify  ******************/

//_______ task for beautifying css
gulp.task('beautify-rtl', function() {
    return gulp.src('Admix/assets/css-rtl/*.css')
        .pipe(beautify.css({ indent_size: 4 }))
        .pipe(gulp.dest('Admix/assets/css-rtl'));
});



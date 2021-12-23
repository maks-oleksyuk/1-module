let project_folder = "css";
let sourse_folder = "src";

let path = {
  build: {
    css: project_folder + "/",
  },
  src: {
    css: "scss/style.scss",
  },
  watch: {
    css: "scss/**/*.scss",
  },
  clean: project_folder + "/",
};

let { src, dest } = require("gulp"),
  gulp = require("gulp"),
  del = require("del"),
  scss = require("gulp-sass")(require("node-sass")),
  autoprefixer = require("gulp-autoprefixer"),
  gcmq = require("gulp-group-css-media-queries"),
  browserSync = require('browser-sync').create();

function css() {
  return gulp
    .src(path.src.css)
    .pipe(
      scss({
        // outputStyle: "expanded",
      })
    )
    .pipe(
      autoprefixer({
        overrideBrowserslist: ["last 5 version"],
        cascade: true,
      })
    )
    // .pipe(gcmq())
    .pipe(dest(path.build.css))
    .pipe(browserSync.stream());
}

function watchFiles() {
  gulp.watch([path.watch.css], css);
}

function browser_Sync(params) {
  browserSync.init({
    proxy: "local.site"
  });
}

function clean() {
  return del(path.clean);
}

let build = gulp.series(clean, css);
let watch = gulp.parallel(build, watchFiles, browser_Sync);

exports.css = css;
exports.build = build;
exports.watch = watch;
exports.default = watch;

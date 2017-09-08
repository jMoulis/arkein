exports.config = {
  files: {
    stylesheets: {
      joinTo: 'css/app.css',
      order: {
        before: 'app/styles/css/app/reset.css',
      },
    },
    javascripts: {
      joinTo: {
        'js/app.js': /^app/,
        'js/vendor.js': /^(?!app)/
      }
    },
  },
  plugins: {
    postcss: {
      processors: [require('autoprefixer')],
    },
    babel: {
      presets: ["env", "react"],
      plugins: [
        "transform-object-rest-spread",
        "transform-class-properties"
      ]
    }
  },
};

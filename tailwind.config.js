module.exports = {
  prefix: 'rgnmhn-', // <- prefix should be placed **before** `content`
  content: ['./templates/**/*.php', './admin/templates/**/*.php', './includes/*.php'],
  theme: {
    extend: {
      fontSize: {
        base: '0.90rem'
      }
    }
  },
  plugins: [],
}

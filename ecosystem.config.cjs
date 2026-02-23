module.exports = {
  apps: [{
    name: 'faj-site',
    script: 'php',
    args: '-S 0.0.0.0:3000 -t /home/user/webapp /home/user/webapp/router.php',
    watch: false,
    instances: 1,
    exec_mode: 'fork'
  }]
}

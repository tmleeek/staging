/*global module:false*/
module.exports = function(grunt) {

	// nom du projet magento cible dans le workspace
	var target = grunt.option('target') || 'mag_aomagento';
	
  // Project configuration.
  grunt.initConfig({
    // Task configuration.
	  
	watch: {
		frontend: {
			files: '../skin/frontend/**/*',
			tasks: ['copy:frontend']
		},
		backend: {
			files: '../app/design/frontend/**/*',
			tasks: ['copy:backend']
		}
    },
	
	copy: {
		
		frontend: {
			files: [
				{expand: true, cwd: '../skin/frontend/', src: ['**'], dest: '../../' + target + '/server/skin/frontend/'}
			]
		},
		backend: {
			files: [
				{expand: true, cwd: '../app/design/frontend/', src: ['**'], dest: '../../' + target + '/server/app/design/frontend/'}
			]
		}
    
	}
  
  });

  // These plugins provide necessary tasks.
  grunt.loadNpmTasks('grunt-contrib-watch');
  grunt.loadNpmTasks('grunt-contrib-copy');

  // Default task.
  grunt.registerTask('default', ['copy', 'watch']);

};

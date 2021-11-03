fromStream('user-70a4bba4-52df-4580-8607-8905e5f8b62d')
.when({
	$init: function() {
		return {
			count: 0,
			emails: []
		}
	},
	"Users\\Domain\\User\\Events\\EmailChangedEvent": function(state, event) {
		state.count += 1;
		state.emails.push(event.data.email);
	}
})

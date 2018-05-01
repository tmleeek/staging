
	sweettooth = (typeof sweettooth === "undefined") ? {} : sweettooth;
	sweettooth.milestone = (typeof sweettooth.milestone === "undefined") ? {} : sweettooth.milestone;

	document.observe('dom:loaded', function()
	{
		sweettooth.milestone.init();
	});

	sweettooth.milestone.init = function ()
	{
		var self = this;
		var conditionsField = $('rule_points_conditions');
		var actionsField = $('rule_points_action');
			
		self.milestoneFields = (typeof self.milestoneFields === "undefined") ? {} : self.milestoneFields;
		self.milestoneActions = (typeof self.milestoneActions === "undefined") ? {} : self.milestoneActions;		
		
		conditionsField.observe('change', function(){
			self.conditionsListener(this);
		});
		
		actionsField.observe('change', function(){
			self.actionListener(this);
		});
		
		initalCondition = conditionsField.value;
		this.showConditionField(initalCondition);
		this.refreshActions(initalCondition);
		
		return self;
	}; 

	sweettooth.milestone.conditionsListener = function(element)
	{
		var condition = element.value;
		this.showConditionField(condition);
		this.refreshActions(condition);
		
		return this;
	};

	sweettooth.milestone.actionListener = function(element)
	{
		var action = element.value;
		this.showActionField(action);
		
		return this;
	};
	
	/**
	* Hides all condition fields except for the condition which was passed in.	
	*/
	sweettooth.milestone.showConditionField = function(condition)
	{
		var self = this;
		
		// Hide everything first
		$H(self.milestoneFields).each(function(object){
			var conditionObject = object.value;
			var element = $(conditionObject.elementId);
			element.up('tr').hide();
			element.removeClassName('required-entry');
			if (!Validation.validate(element)){
                element.value = '';
            } 
		});
		
		// Show only what's needed
		if (typeof self.milestoneFields[condition] !== "undefined"){
			var conditionObject = self.milestoneFields[condition];
			var element = $(conditionObject.elementId);
			element.up('tr').show();
			if (conditionObject.isRequired){
				element.addClassName('required-entry');
			}			
		}
		
		return self;
	};
	
	/**
	* Generates a list of all milestone actions
	* 
	* @param string condition. Condition code to base the actions on
	* @return self
	*/
	sweettooth.milestone.refreshActions = function(condition)
	{
		var self = this;
		var actionsField = $('rule_points_action');
		
		// Prepare a list of all possible milestone actions
		var allActions = {};
		$H(self.milestoneActions).each(function(object){
			var actionObject = object.value;
			$H(actionObject).each(function(object){
				var action = object.key;
				allActions[action] = true;
			});
		});

		// Go through current available actions and remove only what we don't need.
		var availableActions = {};
		actionsField.childElements().each(function(item){
			// debugger;
			currentAction = item.readAttribute('value');
			var isAvailable = true;
			if (typeof allActions[currentAction] === "undefined"){				
				// Non-milestone action. Don't remove.				
				
			} else {
				if (typeof self.milestoneActions[condition] !== "undefined"){
					actionObject = self.milestoneActions[condition];
					if (actionObject[currentAction] === "undefined"){
						// Remove milestone action if condition does not support this action
						item.remove();
						isAvailable = false;
					}
				} else {				
					// Remove milestone action if the condition is non-milestone related.
					item.remove();
					isAvailable = false;
				}				
			}
			
			availableActions[currentAction] = isAvailable;
		});
		
		// Make sure our available actions isn't missing anything we do actually support
		if (typeof self.milestoneActions[condition] !== undefined){
			$H(self.milestoneActions[condition]).each(function(object){
				var action = object.key;
				var label = object.value;				
				if (typeof availableActions[action] === undefined || !availableActions[action]){
					actionsField.insert('<option value="' + action + '">' + label + '</option>');
				}
			});	
		}
		
		
		self.showActionField(actionsField.value);
		
		return self;
	};
	
	/**
	 * Shows the appropriate field based on the action selected
	 * @param string action, name of the action 
	 * @returns self
	 */
	sweettooth.milestone.showActionField = function(action)
	{
		var customergroupField = $('rule_customer_group_id');
		var pointsAmountField = $('rule_points_amount');
		
		switch (action){
			case 'customergroup':
				customergroupField.addClassName('required-entry');
				customergroupField.up('tr').show();
				
				pointsAmountField.removeClassName('required-entry');
				pointsAmountField.up('tr').hide();
				if (!Validation.validate(pointsAmountField)){
					pointsAmountField.value = '';
				}				
				break;
				
			default:				            
				customergroupField.removeClassName('required-entry');
				customergroupField.up('tr').hide();
                        
				pointsAmountField.addClassName('required-entry');
				pointsAmountField.up('tr').show();
				break;
		}
		
		return this;		
	};
	

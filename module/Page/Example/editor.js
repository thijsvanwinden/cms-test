var Editor = Class.create({
    init: function(element, options)
    {
        
    },
    destroy: function()
    {
        
    }
});

var Layout = Class.create({
    getSlots: function()
    {
        if(this.slots){
            this.slots.refresh(this.element, this.options.slotSelector);
        } else {
            this.slots = SlotStack.fromArray(this.element, this.options.slotSelector);
        }
        return this.slots;
    },
    getNodes: function()
    {
        if(this.nodes){
            this.nodes.refresh(this.element, this.options.nodeSelector);
        } else {
            this.nodes = NodeStack.fromArray(this.element, this.options.nodeSelector);
        }      
    }    
});

var Node = Class.create({
        
});

var Slot = Class.create({});


var NodeStack = Class.create({
    
    });

NodeStack.fromParent = function(element)
{
    
    
    return new NodeStack();
}

var NodeResolver = Class.create({
    resolve: function(slotId)
    {
        return Node
    }
});




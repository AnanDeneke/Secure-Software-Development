/* Java is technically always pass-by-value, but this behaves differently depending on whether the 
variable is a primitive type or an object reference. This distinction matters for security because it 
determines whether a function can permanently alter data it was only supposed to read 
(Richardson & Thies, 2012, note that guarding against unintended data changes by reference is a 
core class-security practice). */

public class PassExample { 

    // Primitive passed by value: a copy of the int is made 
    public static void modifyPrimitive(int number) { 
        number = number + 100; 
        System.out.println("Inside modifyPrimitive: " + number); 
    } 
    // Object reference passed by value: the reference is copied, 
    // but it still points to the same object in memory 
    public static void modifyObject(StringBuilder text) { 
        text.append(" - modified"); 
        System.out.println("Inside modifyObject: " + text); 
    } 
    // Reassigning the reference itself does NOT affect the caller's variable 
    public static void reassignObject(StringBuilder text) { 
        text = new StringBuilder("Brand new object"); 
        System.out.println("Inside reassignObject: " + text); 
    } 
    public static void main(String[] args) { 
        int myNumber = 5; 
        modifyPrimitive(myNumber); 
        System.out.println("After modifyPrimitive, myNumber: " + myNumber); 
        // Output: 5 (unchanged, because only a copy was modified) 
        StringBuilder myText = new StringBuilder("Original"); 
        modifyObject(myText); 
        System.out.println("After modifyObject, myText: " + myText); 
        // Output: "Original - modified" (the actual object WAS changed) 
        reassignObject(myText); 
        System.out.println("After reassignObject, myText: " + myText); 
        // Output: "Original - modified" (still unchanged, reassignment inside 
        // the method only affected the local copy of the reference) 
    } 
} 
/*
Explanation: 
• modifyPrimitive receives a copy of the integer 5. Changing that copy inside the method 
has no effect on myNumber back in main, because primitives are copied entirely. 
• modifyObject receives a copy of the reference to the StringBuilder object, not a copy of 
the object itself. Since both the original variable and the copy point to the same object in 
memory, calling .append() changes the actual object, and that change is visible after the 
method returns. 
• reassignObject shows the limit of this behavior: assigning a brand-new object to the local 
parameter text only redirects that local copy of the reference. It does not change what 
myText in main points to, because the reference itself was passed by value, so 
reassigning it inside the method has no effect on the caller's variable.
*/
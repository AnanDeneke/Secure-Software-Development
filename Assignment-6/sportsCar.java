public class sportsCar extends Vehicle {
    @Override
    public void forward() {
        System.out.println("sportsCar launching forward at high speed.");
    }

    @Override
    public void reverse() {
        System.out.println("sportsCar reversing carefully, low ground clearance.");
    }
}
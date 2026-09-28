public class Hybrid extends Vehicle {
    @Override
    public void forward() {
        System.out.println("Hybrid switching to electric motor, moving forward silently.");
    }

    @Override
    public void reverse() {
        System.out.println("Hybrid reversing on electric power.");
    }
}

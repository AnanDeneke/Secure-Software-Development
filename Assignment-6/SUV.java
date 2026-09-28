public class SUV extends Vehicle {
    @Override
    public void forward() {
        System.out.println("SUV shifting into 4WD and moving forward.");
    }

    @Override
    public void reverse() {
        System.out.println("SUV backing up, rear camera active.");
    }
}
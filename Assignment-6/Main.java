public class Main {
    public static void main(String[] args) {
        Vehicle[] fleet = { new SUV(), new sportsCar(), new Hybrid() };

        for (Vehicle v : fleet) {
            v.forward();
            v.reverse();
        }
    }
}
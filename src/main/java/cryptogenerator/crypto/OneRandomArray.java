package cryptogenerator.crypto;

import java.io.BufferedWriter;
import java.io.FileOutputStream;
import java.io.FileWriter;
import java.io.IOException;
import java.security.SecureRandom;

public class OneRandomArray {


    // to fill with random bytes.
    public byte[] fourMegabyteSequence()
    {
        byte[] bytes = new byte[4000000];
        new SecureRandom().nextBytes(bytes);
        return bytes;

    }

}


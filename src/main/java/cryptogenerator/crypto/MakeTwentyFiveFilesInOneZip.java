package cryptogenerator.crypto;

import java.io.FileNotFoundException;
import java.io.FileOutputStream;
import java.io.IOException;
import java.util.zip.ZipEntry;
import java.util.zip.ZipOutputStream;

public class MakeTwentyFiveFilesInOneZip {
    int fileNameStart = 1;
    String[] arrayOfFileNames = new String[25];

    public MakeTwentyFiveFilesInOneZip() {
        for (int i = 0; i < 25; i++) {
            arrayOfFileNames[i] = String.valueOf(i + 1);
            System.out.println(arrayOfFileNames[i]);
        }

    }

    public void createZip() throws IOException {
        FileOutputStream fos1 = new FileOutputStream("flower1.zip");
        ZipOutputStream zos1 = new ZipOutputStream(fos1);
        FileOutputStream fos2 = new FileOutputStream("flower2.zip");
        ZipOutputStream zos2 = new ZipOutputStream(fos2);

        for (int i = 0; i < arrayOfFileNames.length; i++) {
            addToZipFile(arrayOfFileNames[i], zos1,zos2);
        }

        zos1.close();
        fos1.close();
        zos2.close();
        fos2.close();
    }

    private void addToZipFile(String fileName, ZipOutputStream zos1,ZipOutputStream zos2) throws FileNotFoundException, IOException {

        System.out.println("Writing '" + fileName + "' to zip file");

        byte[] s1 = new OneRandomArray().fourMegabyteSequence();
        s1[7] = 0;
        byte[] s2 = s1.clone();
        s2[7] = 119;

        /* File is not on the disk, test.txt indicates
     only the file name to be put into the zip */
        ZipEntry entry1 = new ZipEntry(fileName);
        ZipEntry entry2 = new ZipEntry(fileName);

        zos1.putNextEntry(entry1);
        zos1.write(s1);
        zos1.closeEntry();
        zos2.putNextEntry(entry2);
        zos2.write(s2);
        zos2.closeEntry();


    }
}

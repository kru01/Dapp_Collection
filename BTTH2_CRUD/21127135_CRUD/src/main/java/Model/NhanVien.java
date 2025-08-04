package Model;

import org.springframework.data.annotation.Id;
import org.springframework.data.mongodb.core.mapping.Document;

@Document(collection = "nhanvien")
public class NhanVien {

	@Id
	private String manv;
	private String hoten;
	private int soNamKinhNghiem;
	private boolean thuviec;

	public String getManv() {
		return manv;
	}

	public void setManv(String manv) {
		this.manv = manv;
	}

	public String getHoten() {
		return hoten;
	}

	public void setHoten(String hoten) {
		this.hoten = hoten;
	}

	public int getSoNamKinhNghiem() {
		return soNamKinhNghiem;
	}

	public void setSoNamKinhNghiem(int soNamKinhNghiem) {
		this.soNamKinhNghiem = soNamKinhNghiem;
	}

	public boolean isThuviec() {
		return thuviec;
	}

	public void setThuviec(boolean thuviec) {
		this.thuviec = thuviec;
	}

	public NhanVien(String manv, String hoten, int soNamKinhNghiem, boolean thuviec) {
		super();
		this.manv = manv;
		this.hoten = hoten;
		this.soNamKinhNghiem = soNamKinhNghiem;
		this.thuviec = thuviec;
	}
}
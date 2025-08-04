package com.example.demo;

import org.springframework.boot.SpringApplication;
import org.springframework.boot.autoconfigure.SpringBootApplication;
import org.springframework.cloud.openfeign.EnableFeignClients;
import org.springframework.stereotype.Controller;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.web.bind.annotation.RequestMapping;
import org.springframework.web.servlet.ModelAndView;

@SpringBootApplication
@EnableFeignClients
@Controller
public class Application {

	public static void main(String[] args) {
		SpringApplication.run(Application.class, args);
	}
	
	@Autowired
	QuanLyNhanSuClient client;
	
	@RequestMapping(value = "/employee")
	public ModelAndView LayThongTinNhanVien() {
		String info = client.LayThongTinNhanVien();
		return new ModelAndView("nhanvien.html", "info", info);
	}
}

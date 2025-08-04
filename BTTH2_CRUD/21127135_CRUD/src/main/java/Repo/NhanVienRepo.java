package Repo;

import org.springframework.data.mongodb.repository.MongoRepository;

import Model.NhanVien;

public interface NhanVienRepo extends MongoRepository<NhanVien, String> {
// CRUD
}